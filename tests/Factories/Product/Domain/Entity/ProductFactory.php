<?php

namespace App\Tests\Factories\Product\Domain\Entity;

class ProductFactory extends PersistingProductFactory
{
    protected function initialize(): static
    {
        return $this
            ->withoutPersisting();
    }
}