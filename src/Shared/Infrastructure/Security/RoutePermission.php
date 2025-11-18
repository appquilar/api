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
    case USER_UPDATE_ADDRESS = 'users_update_address';
    case USER_CHANGE_PASSWORD = 'users_change_password';

    /** COMPANY */
    case COMPANY_CREATE = 'company_create';
    case COMPANY_UPDATE = 'company_update';
    case COMPANY_LIST_USERS = 'company_list_users';
    case COMPANY_ADD_USER = 'company_add_user';
    case COMPANY_UPDATE_USER_ROLE = 'company_update_user_role';
    case COMPANY_REMOVE_USER = 'company_remove_user';
    case COMPANY_USER_ACCEPTS_INVITATION = 'company_user_accepts_invitation';

    /** MEDIA */
    case MEDIA_UPLOAD_IMAGE = 'media_upload_image';
    case MEDIA_DELETE_IMAGE = 'media_delete_image';

    /** CATEGORY */
    case CATEGORY_CREATE = 'category_create';
    case CATEGORY_UPDATE = 'category_update';

    /** SITE */
    case SITE_CREATE = 'site_create';
    case SITE_UPDATE = 'site_update';
    case SITE_LIST_ALL = 'site_list_all';

    /** PRODUCT */
    case PRODUCT_CREATE = 'product_create';
    case PRODUCT_UPDATE = 'product_update';
    case PRODUCT_ARCHIVE = 'product_archive';
    case PRODUCT_PUBLISH = 'product_publish';
    case PRODUCT_UNPUBLISH = 'product_unpublish';
    case PRODUCT_GET_BY_ID = 'product_get_product_by_id';

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
            self::USER_UPDATE_ADDRESS,
            self::USER_CHANGE_PASSWORD,
            self::COMPANY_CREATE,
            self::COMPANY_UPDATE,
            self::COMPANY_LIST_USERS,
            self::COMPANY_ADD_USER,
            self::COMPANY_UPDATE_USER_ROLE,
            self::COMPANY_REMOVE_USER,
            self::MEDIA_UPLOAD_IMAGE,
            self::MEDIA_DELETE_IMAGE,
            self::PRODUCT_CREATE,
            self::PRODUCT_UPDATE,
            self::PRODUCT_ARCHIVE,
            self::PRODUCT_PUBLISH,
            self::PRODUCT_UNPUBLISH,
            self::PRODUCT_GET_BY_ID,
                => [UserRole::REGULAR_USER],
            self::CATEGORY_CREATE,
            self::CATEGORY_UPDATE,
            self::SITE_CREATE,
            self::SITE_UPDATE,
            self::SITE_LIST_ALL,
                => [UserRole::ADMIN],
            default => []
        };
    }
}
