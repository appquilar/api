<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Query\GetProductById;

use App\Product\Application\Query\GetProductById\GetProductByIdQuery;
use App\Product\Application\Query\GetProductById\GetProductByIdQueryHandler;
use App\Product\Application\Query\GetProductById\GetProductByIdQueryResult;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Transformer\ProductTransformer;
use App\Product\Domain\Service\ProductAuthorizationServiceInterface;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\Product\Domain\Entity\ProductFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class GetProductByIdQueryHandlerTest extends UnitTestCase
{
    /** @var ProductRepositoryInterface|MockObject */
    private ProductRepositoryInterface|MockObject $productRepository;

    /** @var ProductTransformer|MockObject */
    private ProductTransformer|MockObject $productTransformer;

    /** @var ProductAuthorizationServiceInterface|MockObject */
    private ProductAuthorizationServiceInterface|MockObject $productAuthorizationService;

    private GetProductByIdQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->productTransformer = $this->createMock(ProductTransformer::class);
        $this->productAuthorizationService = $this->createMock(ProductAuthorizationServiceInterface::class);

        $this->handler = new GetProductByIdQueryHandler(
            $this->productRepository,
            $this->productTransformer,
            $this->productAuthorizationService
        );
    }

    public function test_it_returns_product_when_exists_and_user_can_view(): void
    {
        $productId = Uuid::v4();

        $product = ProductFactory::createOne(['productId' => $productId]);
        $transformedProduct = ['id' => (string) $productId, 'name' => $product->getName()];

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($product);

        $this->productAuthorizationService
            ->expects($this->once())
            ->method('canView')
            ->with($product, 'product.get_by_id.unauthorized');

        $this->productTransformer
            ->expects($this->once())
            ->method('transform')
            ->with($product)
            ->willReturn($transformedProduct);

        $query = new GetProductByIdQuery($productId);

        $result = ($this->handler)($query);

        $this->assertInstanceOf(GetProductByIdQueryResult::class, $result);
        $this->assertSame($transformedProduct, $result->getProduct());
    }

    public function test_it_throws_exception_when_product_not_found(): void
    {
        $productId = Uuid::v4();

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn(null);

        $this->productAuthorizationService
            ->expects($this->never())
            ->method('canView');

        $this->productTransformer
            ->expects($this->never())
            ->method('transform');

        $query = new GetProductByIdQuery($productId);

        $this->expectException(EntityNotFoundException::class);

        ($this->handler)($query);
    }

    public function test_it_throws_exception_when_user_cannot_view_product(): void
    {
        $productId = Uuid::v4();

        $product = ProductFactory::createOne(['productId' => $productId]);

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($product);

        $this->productAuthorizationService
            ->expects($this->once())
            ->method('canView')
            ->with($product, 'product.get_by_id.unauthorized')
            ->willThrowException(new UnauthorizedException('product.get_by_id.unauthorized'));

        $this->productTransformer
            ->expects($this->never())
            ->method('transform');

        $query = new GetProductByIdQuery($productId);

        $this->expectException(UnauthorizedException::class);

        ($this->handler)($query);
    }
}
