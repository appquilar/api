<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Command\ArchiveProduct;

use App\Product\Application\Command\ArchiveProduct\ArchiveProductCommand;
use App\Product\Application\Command\ArchiveProduct\ArchiveProductCommandHandler;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Unit\Product\Application\Command\TestProductCommandHandler;
use Symfony\Component\Uid\Uuid;

class ArchiveTestProductCommandHandler extends TestProductCommandHandler
{
    private ArchiveProductCommandHandler $handler;
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new ArchiveProductCommandHandler(
            $this->productRepository,
            $this->productAuthorizationService,
            $this->eventDispatcher
        );
    }

    public function test_archive_product_handle(): void
    {
        $productId = Uuid::v4();
        $product = $this->givenAProductExists($productId);

        $this->givenCanEditProduct($product);
        $this->givenDispatchProductUpdateEvent($productId);
        $product->archive();
        $this->givenISaveTheProduct($product);

        $this->thenTheSutIsExecuted($productId);
    }

    public function test_archive_product_handle_product_not_found(): void
    {
        $productId = Uuid::v4();
        $this->givenAProductNotExists($productId);
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Entity with id %s not found', $productId->toString()));

        $this->thenTheSutIsExecuted($productId);
    }

    public function test_no_permissions_to_edit(): void
    {
        $productId = Uuid::v4();
        $product = $this->givenAProductExists($productId);

        $this->givenCantEditProduct($product);
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('product.update.unauthorized');

        $this->thenTheSutIsExecuted($productId);
    }

    private function thenTheSutIsExecuted(Uuid $productId): void
    {
        $this->handler->__invoke(new ArchiveProductCommand($productId));
    }
}
