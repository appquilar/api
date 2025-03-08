<?php

namespace App\Tests\Factories\Company\Domain\Entity;

use App\Company\Domain\Entity\CompanyUser;
use App\Company\Domain\Enum\CompanyUserRole;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @method static CompanyUser createOne(array $attributes = [])
 */
final class CompanyUserFactory extends PersistingCompanyUserFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->withoutPersisting()
            // ->afterInstantiate(function(CompanyUser $companyUser): void {})
        ;
    }
}
