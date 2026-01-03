<?php

namespace App\Security;

use RuntimeException;

class SecretCipher
{
    private string $key;

    private function __construct(string $key)
    {
        if (!extension_loaded('sodium') && !function_exists('sodium_crypto_secretbox')) {
            throw new RuntimeException('La extensión sodium o su polyfill sodium_compat es requerida.');
        }

        if (strlen($key) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RuntimeException('La clave maestra debe tener 32 bytes.');
        }

        $this->key = $key;
    }

    public static function fromBase64(string $encodedKey): self
    {
        $binaryKey = base64_decode(trim($encodedKey), true);

        if ($binaryKey === false) {
            throw new RuntimeException('La clave maestra codificada no es válida.');
        }

        return new self($binaryKey);
    }

    public static function fromKeyFile(string $path): self
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new RuntimeException('No se pudo leer el archivo de la clave maestra.');
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException('No se pudo obtener el contenido de la clave maestra.');
        }

        return self::fromBase64($contents);
    }

    public function encrypt(string $plaintext): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = sodium_crypto_secretbox($plaintext, $nonce, $this->key);

        return base64_encode($nonce . $cipher);
    }

    public function decrypt(string $encodedCipher): string
    {
        $decoded = base64_decode($encodedCipher, true);

        if ($decoded === false) {
            throw new RuntimeException('El texto cifrado recibido no es válido.');
        }

        $nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        if ($nonce === false || strlen($nonce) !== SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            throw new RuntimeException('No se pudo recuperar el nonce del texto cifrado.');
        }

        $plain = sodium_crypto_secretbox_open($ciphertext, $nonce, $this->key);

        if ($plain === false) {
            throw new RuntimeException('No se pudo descifrar el valor proporcionado.');
        }

        return $plain;
    }

    public function __destruct()
    {
        sodium_memzero($this->key);
    }
}
