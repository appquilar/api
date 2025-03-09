<?php

declare(strict_types=1);

namespace App\Company\Application\Command\CompanyUserAcceptInvitation;

use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class CompanyUserAcceptInvitationCommand implements Command
{
    public function __construct(
        private Uuid $companyId,
        private string $invitationToken,
        private ?string $email = null,
        private ?string $password = null,
    ) {
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }

    public function getInvitationToken(): string
    {
        return $this->invitationToken;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
