<?php

declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Tests\Factories\Company\Domain\Entity\PersistingCompanyFactory;
use App\Tests\Factories\Company\Domain\Entity\PersistingCompanyUserFactory;
use Symfony\Component\Uid\Uuid;

trait CompanyContext
{
    public function givenACompanyWithOwnerAndId(Uuid $ownerId, Uuid $companyId): void
    {
        PersistingCompanyFactory::createOne(['companyId' => $companyId]);
        PersistingCompanyUserFactory::createOne(['companyId' => $companyId, 'userId' => $ownerId, 'companyUserRole' => CompanyUserRole::ADMIN]);
    }
}
