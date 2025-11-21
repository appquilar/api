<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Listener;

use App\Category\Domain\Event\CategoryUpdated;
use App\Product\Domain\Event\ProductCreated;
use App\Product\Domain\Event\ProductUpdated;
use App\Product\Infrastructure\Projection\ProductSearchProjection;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ProductSearchEventListener
{
    public function __construct(
        private ProductSearchProjection $productSearchProjection,
    ) {
    }

    #[AsEventListener(event: ProductCreated::class)]
    public function onProductCreated(ProductCreated $event): void
    {
        $this->productSearchProjection->syncWhenProductEvent($event->getProductId());
    }

    #[AsEventListener(event: ProductUpdated::class)]
    public function onProductUpdated(ProductUpdated $event): void
    {
        $this->productSearchProjection->syncWhenProductEvent($event->getProductId());
    }

    #[AsEventListener(event: CategoryUpdated::class)]
    public function onCategoryUpdated(CategoryUpdated $event): void
    {
        $this->productSearchProjection->syncWhenCategoryEvent($event->getCategoryId());
    }
}
