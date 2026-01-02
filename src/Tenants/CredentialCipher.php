<?php

namespace App\Tenants;

use App\Security\SecretCipher;

class CredentialCipher
{
    private SecretCipher $cipher;

    public function __construct(SecretCipher $cipher)
    {
        $this->cipher = $cipher;
    }

    public function encrypt(string $value): string
    {
        return $this->cipher->encrypt($value);
    }

    public function decrypt(string $encoded): string
    {
        return $this->cipher->decrypt($encoded);
    }
}
