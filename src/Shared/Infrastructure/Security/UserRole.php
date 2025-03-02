<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

enum UserRole: string
{
    case REGULAR_USER = 'ROLE_USER';
    case ADMIN = 'ROLE_ADMIN';

    /**
     * @param UserRole[] $requiredRoles
     */
    public function canAccess(array $requiredRoles): bool
    {
        return match ($this) {
            self::ADMIN => true,
            self::REGULAR_USER => in_array(self::REGULAR_USER, $requiredRoles)
        };
    }

    /**
     * @param UserRole[] $roles
     * @return bool
     */
    public function isAdmin(array $roles): bool
    {
        return match ($this) {
            self::ADMIN => in_array(self::ADMIN, $roles),
            default => false
        };
    }
}
