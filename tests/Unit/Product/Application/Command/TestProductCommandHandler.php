<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Command;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Event\ProductUpdated;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\Product\Domain\Entity\ProductFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

abstract class TestProductCommandHandler extends UnitTestCase
{
    protected ProductRepositoryInterface|MockObject $productRepository;
    protected ProductAuthorizationServiceInterface|MockObject $productAuthorizationService;
    protected EventDispatcherInterface|MockObject $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->productAuthorizationService = $this->createMock(ProductAuthorizationServiceInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    protected function givenAProductExists(Uuid $productId): Product
    {
        $product = ProductFactory::createOne(['productId' => $productId]);
        $this->productRepository->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($product);

        return $product;
    }

    protected function givenAProductNotExists(Uuid $productId): void
    {
        $this->productRepository->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn(null);
    }

    protected function givenCanEditProduct(Product $product): void
    {
        $this->productAuthorizationService->expects($this->once())
            ->method('canEdit')
            ->with($product, 'product.update.unauthorized');
    }

    protected function givenCantEditProduct(Product $product): void
    {
        $this->productAuthorizationService->expects($this->once())
            ->method('canEdit')
            ->with($product, 'product.update.unauthorized')
            ->willThrowException(new UnauthorizedException('product.update.unauthorized'));
    }

    protected function givenDispatchProductUpdateEvent(Uuid $productId): void
    {
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with(new ProductUpdated($productId));
    }

    protected function givenISaveTheProduct(Product $product): void
    {
        $this->productRepository->expects($this->once())
            ->method('save')
            ->with($product);
    }
}
