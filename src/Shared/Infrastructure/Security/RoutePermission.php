<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

enum RoutePermission: string
{
    /** AUTH */
    case AUTH_LOGOUT = '/api/auth/logout';

    /** USER */
    case USER_ME = '/api/me';
    case USER_GET_BY_ID = 'users_get_user_by_id';
    case USER_UPDATE_USER = 'users_update_user';
    case USER_CHANGE_PASSWORD = 'users_change_password';

    /**
     * @return UserRole[]
     */
    public function getRequiredRoles(): array
    {
        return match ($this) {
            self::AUTH_LOGOUT,
            self::USER_ME,
            self::USER_GET_BY_ID,
            self::USER_UPDATE_USER,
            self::USER_CHANGE_PASSWORD,
                => [UserRole::REGULAR_USER],
        };
    }
}
