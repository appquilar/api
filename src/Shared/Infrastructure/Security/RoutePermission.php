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

    /** COMPANY */
    case COMPANY_CREATE = 'company_create';
    case COMPANY_LIST_USERS = 'company_list_users';
    case COMPANY_ADD_USER = 'company_add_user';
    case COMPANY_UPDATE_USER_ROLE = 'company_update_user_role';
    case COMPANY_REMOVE_USER = 'company_remove_user';
    case COMPANY_USER_ACCEPTS_INVITATION = 'company_user_accepts_invitation';

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
            self::COMPANY_CREATE,
            self::COMPANY_LIST_USERS,
            self::COMPANY_ADD_USER,
            self::COMPANY_UPDATE_USER_ROLE,
            self::COMPANY_REMOVE_USER,
                => [UserRole::REGULAR_USER],
            default => []
        };
    }
}
