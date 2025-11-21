<?php

namespace App\Tests\Factories\Product\Domain\Entity;

use App\Product\Domain\Entity\Product;
use App\Product\Infrastructure\Projection\ProductSearchProjection;
use App\Tests\Factories\Product\ProductFactoryDefaultsTrait;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

class PersistingProductFactory extends PersistentObjectFactory
{
    use ProductFactoryDefaultsTrait;

    public function __construct(
        private ProductSearchProjection $productSearchProjection,
    ) {
        parent::__construct();
    }

    protected function defaults(): array|callable
    {
        return $this->buildProductDefaults();
    }

    protected function initialize(): static
    {
        return $this->with($this->defaults())
            ->afterPersist(
                function (Product $product) {
                    $this->productSearchProjection->syncWhenProductEvent($product->getId());
                }
            );
    }
}