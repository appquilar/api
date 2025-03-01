<?php

declare(strict_types=1);

namespace App\User\Application\Service;

class UserPasswordHasher
{
    private const HASH_PASSWORD = PASSWORD_ARGON2ID;
    public function hashPassword(string $plainPassword): string
    {
        return password_hash($plainPassword, self::HASH_PASSWORD);
    }

    public function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }
}
