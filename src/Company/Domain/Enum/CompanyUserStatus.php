<?php

declare(strict_types=1);

namespace App\Company\Domain\Enum;

enum CompanyUserStatus: string
{
    case PENDING = 'PENDING';
    case ACCEPTED = 'ACCEPTED';
    case EXPIRED = 'EXPIRED';

    public function getStatus(\DateTimeImmutable $expiresAt): self
    {
        return match(
            $expiresAt > new \DateTimeImmutable() &&
            $this !== self::ACCEPTED
        ) {
            true => self::EXPIRED,
            false => $this
        };
    }
}
