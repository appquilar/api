<?php declare(strict_types=1);

namespace App\Rent\Domain\Enum;

enum RentOwnerType: string
{
    case COMPANY = 'COMPANY';
    case USER = 'USER';
}
