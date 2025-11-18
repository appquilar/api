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
            'parentId' => null,
            'iconId' => Uuid::v4(),
            'featuredImageId' => Uuid::v4(),
            'landscapeImageId' => Uuid::v4(),
        ];
    }

    protected function initialize(): static
    {
        return $this->with($this->defaults());
    }
}
