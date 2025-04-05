<?php

declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Tests\Factories\Site\Domain\Entity\PersistingSiteFactory;
use Symfony\Component\Uid\Uuid;

trait SiteContext
{
    public function givenItExistsASiteWithId(Uuid $siteId): void
    {
        PersistingSiteFactory::createOne(['siteId' => $siteId]);
    }
}
