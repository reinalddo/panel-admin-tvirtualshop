<?php

namespace App\Auth;

use RuntimeException;

final class AuthService
{
    private const SESSION_KEY = 'panel_master_admin_id';

    private AdminRepository $repository;

    public function __construct(AdminRepository $repository)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException('La sesión debe iniciarse antes de usar AuthService.');
        }

        $this->repository = $repository;
    }

    public function attempt(string $email, string $password): bool
    {
        $admin = $this->repository->findActiveByEmail($email);

        if ($admin === null) {
            return false;
        }

        if (!password_verify($password, $admin['password_hash'])) {
            return false;
        }

        $_SESSION[self::SESSION_KEY] = (int) $admin['id'];
        $this->repository->touchLogin((int) $admin['id']);

        return true;
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }

    public function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public function userId(): ?int
    {
        return $this->check() ? (int) $_SESSION[self::SESSION_KEY] : null;
    }
}
