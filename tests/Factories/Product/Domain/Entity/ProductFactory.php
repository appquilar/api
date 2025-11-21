<?php

namespace App\Tests\Factories\Product\Domain\Entity;

use App\Tests\Factories\Product\ProductFactoryDefaultsTrait;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

class ProductFactory extends PersistentObjectFactory
{
    use ProductFactoryDefaultsTrait;

    protected function initialize(): static
    {
        return $this
            ->withoutPersisting();
    }

    protected function defaults(): array|callable
    {
        return $this->buildProductDefaults();
    }
}