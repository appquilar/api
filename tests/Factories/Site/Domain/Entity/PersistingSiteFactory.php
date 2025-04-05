<?php

namespace App\Tests\Factories\Site\Domain\Entity;

use App\Site\Domain\Entity\Site;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Site>
 */
class PersistingSiteFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Site::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'siteId' => Uuid::v4(),
            'categoryIds' => [],
            'description' => self::faker()->text(),
            'faviconId' => Uuid::v4(),
            'featuredCategoryIds' => [],
            'logoId' => Uuid::v4(),
            'menuCategoryIds' => [],
            'name' => self::faker()->text(),
            'primaryColor' => self::faker()->text(6),
            'title' => self::faker()->text(),
            'url' => self::faker()->url(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Site $site): void {})
        ;
    }
}
