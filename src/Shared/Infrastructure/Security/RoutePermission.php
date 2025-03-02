<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

enum RoutePermission: string
{
    case AUTH_LOGOUT = '/api/auth/logout';
    case USER_ME = '/api/me';
    case USER_GET_BY_ID = 'users_get_user_by_id';

    /**
     * @return UserRole[]
     */
    public function getRequiredRoles(): array
    {
        return match ($this) {
            self::AUTH_LOGOUT,
            self::USER_ME,
            self::USER_GET_BY_ID,
                => [UserRole::REGULAR_USER],
        };
    }
}
