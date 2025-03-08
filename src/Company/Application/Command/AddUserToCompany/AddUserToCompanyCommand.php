<?php

declare(strict_types=1);

namespace App\Company\Application\Command\AddUserToCompany;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class AddUserToCompanyCommand implements Command
{
    public function __construct(
        private Uuid $companyId,
        private CompanyUserRole $role,
        private ?Uuid $userId = null,
        private ?string $email = null
    ) {
    }

    public function getUserId(): ?Uuid
    {
        return $this->userId;
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }

    public function getRole(): CompanyUserRole
    {
        return $this->role;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
