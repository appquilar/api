<?php

namespace App\Tests\Factories\Category\Domain\Entity;

use App\Category\Domain\Entity\Category;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Category>
 */
class CategoryFactory extends PersistingCategoryFactory
{
    protected function initialize(): static
    {
        return $this
            ->withoutPersisting()
            // ->afterInstantiate(function(Category $category): void {})
        ;
    }
}
