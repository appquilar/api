<?php

namespace App\Tests\Factories\Category\Domain\Entity;

use App\Category\Domain\Entity\Category;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Category>
 */
class PersistingCategoryFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Category::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'categoryId' => Uuid::v4(),
            'name' => self::faker()->word(),
            'description' => self::faker()->text(255),
            'slug' => self::faker()->slug(),
            'parentId' => Uuid::v4(),
            'iconId' => Uuid::v4(),
            'featuredImageId' => Uuid::v4(),
            'landscapeImageId' => Uuid::v4(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Category $category): void {})
        ;
    }
}
