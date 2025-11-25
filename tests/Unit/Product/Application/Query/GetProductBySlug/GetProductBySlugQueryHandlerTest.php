<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Query\GetProductBySlug;

use App\Product\Application\Query\GetProductBySlug\GetProductBySlugQuery;
use App\Product\Application\Query\GetProductBySlug\GetProductBySlugQueryHandler;
use App\Product\Application\Query\GetProductBySlug\GetProductBySlugQueryResult;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Transformer\ProductTransformer;
use App\Product\Domain\Service\ProductAuthorizationServiceInterface;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\Product\Domain\Entity\ProductFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class GetProductBySlugQueryHandlerTest extends UnitTestCase
{
    /** @var ProductRepositoryInterface|MockObject */
    private ProductRepositoryInterface|MockObject $productRepository;

    /** @var ProductAuthorizationServiceInterface|MockObject */
    private ProductAuthorizationServiceInterface|MockObject $productAuthorizationService;

    /** @var ProductTransformer|MockObject */
    private ProductTransformer|MockObject $productTransformer;

    private GetProductBySlugQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->productAuthorizationService = $this->createMock(ProductAuthorizationServiceInterface::class);
        $this->productTransformer = $this->createMock(ProductTransformer::class);

        $this->handler = new GetProductBySlugQueryHandler(
            $this->productRepository,
            $this->productAuthorizationService,
            $this->productTransformer
        );
    }

    public function test_it_returns_product_when_found_and_published_and_user_can_view(): void
    {
        $slug = 'some-product-slug';

        // Creamos un producto y lo marcamos como publicado
        $product = ProductFactory::createOne(['slug' => $slug]);
        // Asumimos que el dominio tiene un método publish() que marca isPublished = true
        $product->publish();

        $transformedProduct = ['id' => (string) $product->getId(), 'name' => $product->getName()];

        $this->productRepository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn($product);

        $this->productAuthorizationService
            ->expects($this->once())
            ->method('canViewIfPublic')
            ->with($product, 'product.get_by_slug.unauthorized');

        $this->productTransformer
            ->expects($this->once())
            ->method('transform')
            ->with($product)
            ->willReturn($transformedProduct);

        $query = new GetProductBySlugQuery($slug);

        $result = ($this->handler)($query);

        $this->assertInstanceOf(GetProductBySlugQueryResult::class, $result);
        $this->assertSame($transformedProduct, $result->getProduct());
    }

    public function test_it_throws_not_found_when_product_does_not_exist(): void
    {
        $slug = 'non-existing-slug';

        $this->productRepository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn(null);

        $this->productAuthorizationService
            ->expects($this->never())
            ->method('canViewIfPublic');

        $this->productTransformer
            ->expects($this->never())
            ->method('transform');

        $query = new GetProductBySlugQuery($slug);

        $this->expectException(NotFoundException::class);

        ($this->handler)($query);
    }

    public function test_it_throws_not_found_when_product_is_not_published(): void
    {
        $slug = 'unpublished-product-slug';

        // Creamos un producto, por defecto lo dejamos sin publicar (isPublished = false)
        $product = ProductFactory::createOne(['slug' => $slug]);
        // No llamamos a publish(), para forzar isPublished() = false

        $this->productRepository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn($product);

        $this->productAuthorizationService
            ->expects($this->never())
            ->method('canViewIfPublic');

        $this->productTransformer
            ->expects($this->never())
            ->method('transform');

        $query = new GetProductBySlugQuery($slug);

        $this->expectException(NotFoundException::class);

        ($this->handler)($query);
    }

    public function test_it_throws_unauthorized_when_user_cannot_view_public_product(): void
    {
        $slug = 'some-public-product-slug';

        $product = ProductFactory::createOne(['slug' => $slug]);
        $product->publish();

        $this->productRepository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn($product);

        $this->productAuthorizationService
            ->expects($this->once())
            ->method('canViewIfPublic')
            ->with($product, 'product.get_by_slug.unauthorized')
            ->willThrowException(new UnauthorizedException('product.get_by_slug.unauthorized'));

        $this->productTransformer
            ->expects($this->never())
            ->method('transform');

        $query = new GetProductBySlugQuery($slug);

        $this->expectException(UnauthorizedException::class);

        ($this->handler)($query);
    }
}
