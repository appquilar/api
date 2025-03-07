<?php

declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Tests\Factories\Company\Domain\Entity\PersistingCompanyFactory;
use Symfony\Component\Uid\Uuid;

trait CompanyContext
{
    public function givenACompanyWithOwnerAndId(Uuid $ownerId, Uuid $companyId): void
    {
        PersistingCompanyFactory::createOne(['companyId' => $companyId, 'ownerId' => $ownerId]);
    }
}
