<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Infrastructure\Listener;

use App\Category\Domain\Event\CategoryUpdated;
use App\Product\Domain\Event\ProductCreated;
use App\Product\Domain\Event\ProductUpdated;
use App\Product\Infrastructure\Listener\ProductSearchEventListener;
use App\Product\Infrastructure\Projection\ProductSearchProjection;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class ProductSearchEventListenerTest extends UnitTestCase
{
    /** @var ProductSearchProjection|MockObject */
    private ProductSearchProjection|MockObject $projection;

    private ProductSearchEventListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projection = $this->createMock(ProductSearchProjection::class);

        $this->listener = new ProductSearchEventListener(
            $this->projection
        );
    }

    public function test_on_product_created_calls_projection_sync_when_product_event(): void
    {
        $productId = Uuid::v4();

        /** @var ProductCreated|MockObject $event */
        $event = $this->createMock(ProductCreated::class);
        $event->method('getProductId')->willReturn($productId);

        $this->projection
            ->expects($this->once())
            ->method('syncWhenProductEvent')
            ->with($productId);

        $this->listener->onProductCreated($event);
    }

    public function test_on_product_updated_calls_projection_sync_when_product_event(): void
    {
        $productId = Uuid::v4();

        /** @var ProductUpdated|MockObject $event */
        $event = $this->createMock(ProductUpdated::class);
        $event->method('getProductId')->willReturn($productId);

        $this->projection
            ->expects($this->once())
            ->method('syncWhenProductEvent')
            ->with($productId);

        $this->listener->onProductUpdated($event);
    }

    public function test_on_category_updated_calls_projection_sync_when_category_event(): void
    {
        $categoryId = Uuid::v4();

        /** @var CategoryUpdated|MockObject $event */
        $event = $this->createMock(CategoryUpdated::class);
        $event->method('getCategoryId')->willReturn($categoryId);

        $this->projection
            ->expects($this->once())
            ->method('syncWhenCategoryEvent')
            ->with($categoryId);

        $this->listener->onCategoryUpdated($event);
    }
}
