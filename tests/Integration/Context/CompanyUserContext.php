<?php

declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Company\Domain\Entity\CompanyUser;
use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Tests\Factories\Company\Domain\Entity\PersistingCompanyUserFactory;
use Symfony\Component\Uid\Uuid;

trait CompanyUserContext
{
    public function aCompanyHasAnUser(Uuid $companyId, CompanyUserRole $role, CompanyUserStatus $status, ?Uuid $userId = null): void
    {
        PersistingCompanyUserFactory::createOne(['companyId' => $companyId, 'role' => $role, 'status' => $status, 'userId' => $userId]);
    }
}
