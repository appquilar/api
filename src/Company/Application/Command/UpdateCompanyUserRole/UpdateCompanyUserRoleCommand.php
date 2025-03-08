<?php

declare(strict_types=1);

namespace App\Company\Application\Command\UpdateCompanyUserRole;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class UpdateCompanyUserRoleCommand implements Command
{
    public function __construct(
        private Uuid $companyId,
        private Uuid $userId,
        private CompanyUserRole $role
    ) {
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getRole(): CompanyUserRole
    {
        return $this->role;
    }
}
