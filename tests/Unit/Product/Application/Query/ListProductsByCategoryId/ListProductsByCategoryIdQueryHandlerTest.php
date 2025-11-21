<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Query\ListProductsByCategoryId;

use App\Product\Application\Query\ListProductsByCategoryId\ListProductsByCategoryIdQuery;
use App\Product\Application\Query\ListProductsByCategoryId\ListProductsByCategoryIdQueryHandler;
use App\Product\Application\Query\ListProductsByCategoryId\ListProductsByCategoryIdQueryResult;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductCategoryServiceInterface;
use App\Product\Application\Transformer\ProductTransformer;
use App\Product\Domain\Entity\Product;
use App\Tests\Factories\Product\Domain\Entity\ProductFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class ListProductsByCategoryIdQueryHandlerTest extends UnitTestCase
{
    /** @var ProductCategoryServiceInterface|MockObject */
    private ProductCategoryServiceInterface|MockObject $productCategoryService;

    /** @var ProductRepositoryInterface|MockObject */
    private ProductRepositoryInterface|MockObject $productRepository;

    /** @var ProductTransformer|MockObject */
    private ProductTransformer|MockObject $productTransformer;

    private ListProductsByCategoryIdQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productCategoryService = $this->createMock(ProductCategoryServiceInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->productTransformer = $this->createMock(ProductTransformer::class);

        $this->handler = new ListProductsByCategoryIdQueryHandler(
            $this->productCategoryService,
            $this->productRepository,
            $this->productTransformer
        );
    }

    public function test_it_lists_products_by_category_trail_with_pagination(): void
    {
        $categoryId      = Uuid::v4();
        $childCategoryId = Uuid::v4();
        $categoriesTrail = [$categoryId, $childCategoryId];

        $page    = 2;
        $perPage = 5;

        /** @var Product $product1 */
        $product1 = ProductFactory::createOne(['categoryId' => $categoryId]);
        /** @var Product $product2 */
        $product2 = ProductFactory::createOne(['categoryId' => $childCategoryId]);

        $products = [$product1, $product2];

        $total = 12;

        $this->productCategoryService
            ->expects($this->once())
            ->method('getCategoriesTrailIds')
            ->with($categoryId)
            ->willReturn($categoriesTrail);

        $this->productRepository
            ->expects($this->once())
            ->method('paginateByCategoryId')
            ->with($categoriesTrail, $page, $perPage)
            ->willReturn($products);

        $this->productRepository
            ->expects($this->once())
            ->method('countByCategoryId')
            ->with($categoriesTrail)
            ->willReturn($total);

        $transformed1 = ['id' => (string) $product1->getId(), 'name' => $product1->getName()];
        $transformed2 = ['id' => (string) $product2->getId(), 'name' => $product2->getName()];

        $this->productTransformer
            ->expects($this->exactly(2))
            ->method('transform')
            ->withConsecutive(
                [$product1],
                [$product2]
            )
            ->willReturnOnConsecutiveCalls(
                $transformed1,
                $transformed2
            );

        $query = new ListProductsByCategoryIdQuery($categoryId, $page, $perPage);

        /** @var ListProductsByCategoryIdQueryResult $result */
        $result = ($this->handler)($query);

        $this->assertInstanceOf(ListProductsByCategoryIdQueryResult::class, $result);

        $responseData = $result->getResponseData();

        $this->assertSame($total, $responseData['total']);
        $this->assertSame($page, $responseData['page']);
        $this->assertSame([$transformed1, $transformed2], $responseData['data']);
    }

    public function test_it_returns_empty_list_when_no_products_found(): void
    {
        $categoryId = Uuid::v4();
        $categoriesTrail = [$categoryId];

        $page    = 1;
        $perPage = 10;
        $total   = 0;

        $this->productCategoryService
            ->expects($this->once())
            ->method('getCategoriesTrailIds')
            ->with($categoryId)
            ->willReturn($categoriesTrail);

        $this->productRepository
            ->expects($this->once())
            ->method('paginateByCategoryId')
            ->with($categoriesTrail, $page, $perPage)
            ->willReturn([]);

        $this->productRepository
            ->expects($this->once())
            ->method('countByCategoryId')
            ->with($categoriesTrail)
            ->willReturn($total);

        $this->productTransformer
            ->expects($this->never())
            ->method('transform');

        $query = new ListProductsByCategoryIdQuery($categoryId, $page, $perPage);

        /** @var ListProductsByCategoryIdQueryResult $result */
        $result = ($this->handler)($query);

        $responseData = $result->getResponseData();

        $this->assertInstanceOf(ListProductsByCategoryIdQueryResult::class, $result);
        $this->assertSame([], $responseData['data']);
        $this->assertSame($total, $responseData['total']);
        $this->assertSame($page, $responseData['page']);
    }
}
