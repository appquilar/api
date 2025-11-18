<?php declare(strict_types=1);

namespace App\Product\Domain\Enum;

enum ProductOwner: string
{
    case COMPANY = 'COMPANY';
    case USER = 'USER';
}
