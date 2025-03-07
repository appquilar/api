<?php

namespace App\Tests\Factories\Company\Domain\Entity;

use App\Company\Domain\Entity\Company;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Company>
 */
final class CompanyFactory extends PersistingCompanyFactory
{
    protected function initialize(): static
    {
        return $this
            ->withoutPersisting();
    }
}
