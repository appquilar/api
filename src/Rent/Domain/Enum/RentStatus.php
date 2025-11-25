<?php declare(strict_types=1);

namespace App\Rent\Domain\Enum;

enum RentStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::DRAFT     => in_array($target, [self::PENDING, self::CANCELLED], true),
            self::PENDING   => in_array($target, [self::CONFIRMED, self::CANCELLED], true),
            self::CONFIRMED => in_array($target, [self::COMPLETED, self::CANCELLED], true),
            self::COMPLETED, self::CANCELLED => false,
        };
    }

    public static function draft(): self
    {
        return self::DRAFT;
    }
}
