<?php

declare(strict_types=1);

namespace App\Company\Application\Event;

use Symfony\Component\Uid\Uuid;

class CompanyUserCreated
{
    public function __construct(
        private Uuid $companyId,
        private string $email,
        private bool $isOwner,
        private ?string $token = null,
    ) {
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isOwner(): bool
    {
        return $this->isOwner;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
}
