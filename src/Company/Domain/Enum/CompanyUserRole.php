<?php

declare(strict_types=1);

namespace App\Company\Domain\Enum;

enum CompanyUserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case CONTRIBUTOR = 'ROLE_CONTRIBUTOR';

    public static function values(): array
    {
        return array_map(
            static fn (self $case) => $case->value,
            self::cases()
        );
    }
}
