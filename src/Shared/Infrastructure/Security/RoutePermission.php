<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

enum RoutePermission: string
{
    case LOGOUT = '/api/auth/logout';

    /**
     * @return UserRole[]
     */
    public function getRequiredRoles(): array
    {
        return match ($this) {
            self::LOGOUT => [UserRole::REGULAR_USER],
        };
    }
}
