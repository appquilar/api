<?php

declare(strict_types=1);

namespace App\User\Application\Dto;

use Symfony\Component\Uid\Uuid;

class TokenPayload
{
    private const EXPIRATION_TIME = 60*60*24*30; // 1 month

    private ?int $expirationTime;

    public function __construct(
        private Uuid $userId,
        private string $email,
        ?int $expirationTime = null,
        private bool $revoked = false
    ) {
        $this->expirationTime = $expirationTime !== null ? $expirationTime : time() + self::EXPIRATION_TIME;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function getExpirationTime(): ?int
    {
        return $this->expirationTime;
    }

    public function isExpired(): bool
    {
        return $this->expirationTime < time();
    }
}
