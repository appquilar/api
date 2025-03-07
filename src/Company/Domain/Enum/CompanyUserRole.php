<?php

declare(strict_types=1);

namespace App\Company\Domain\Enum;

enum CompanyUserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case CONTRIBUTOR = 'ROLE_CONTRIBUTOR';
}
