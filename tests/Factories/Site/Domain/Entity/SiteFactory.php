<?php

namespace App\Tests\Factories\Site\Domain\Entity;

use App\Site\Domain\Entity\Site;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Site>
 */
class SiteFactory extends PersistingSiteFactory
{
    protected function initialize(): static
    {
        return $this
            ->withoutPersisting()
        ;
    }
}
