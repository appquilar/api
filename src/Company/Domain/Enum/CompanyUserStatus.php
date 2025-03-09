<?php

declare(strict_types=1);

namespace App\Company\Domain\Enum;

enum CompanyUserStatus: string
{
    case PENDING = 'PENDING';
    case ACCEPTED = 'ACCEPTED';
    case EXPIRED = 'EXPIRED';
}
