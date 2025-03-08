<?php

declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Tests\Factories\Company\Domain\Entity\PersistingCompanyFactory;
use App\Tests\Factories\Company\Domain\Entity\PersistingCompanyUserFactory;
use Symfony\Component\Uid\Uuid;

trait CompanyContext
{
    use CompanyUserContext;

    public function givenACompanyWithId(Uuid $companyId): void
    {
        PersistingCompanyFactory::createOne(['companyId' => $companyId]);
    }

    public function givenACompanyWithOwnerAndId(Uuid $ownerId, Uuid $companyId): void
    {
        $this->givenACompanyWithId($companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::ADMIN, CompanyUserStatus::ACCEPTED, $ownerId);
    }
}
