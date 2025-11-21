<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Query\ListProductsByOwner;

use App\Product\Application\Query\ListProductsByOwner\ListProductsByOwnerQuery;
use App\Product\Application\Query\ListProductsByOwner\ListProductsByOwnerQueryHandler;
use App\Product\Application\Query\ListProductsByOwner\ListProductsByOwnerQueryResult;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Transformer\ProductTransformer;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Enum\ProductOwner;
use App\Tests\Factories\Product\Domain\Entity\ProductFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class ListProductsByOwnerQueryHandlerTest extends UnitTestCase
{
    /** @var ProductRepositoryInterface|MockObject */
    private ProductRepositoryInterface|MockObject $productRepository;

    /** @var ProductTransformer|MockObject */
    private ProductTransformer|MockObject $productTransformer;

    private ListProductsByOwnerQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->productTransformer = $this->createMock(ProductTransformer::class);

        $this->handler = new ListProductsByOwnerQueryHandler(
            $this->productRepository,
            $this->productTransformer
        );
    }

    public function test_it_lists_products_by_company_owner_with_pagination(): void
    {
        $companyId = Uuid::v4();
        $page      = 2;
        $perPage   = 5;
        $total     = 7;

        /** @var Product $product1 */
        $product1 = ProductFactory::createOne(['companyId' => $companyId]);
        /** @var Product $product2 */
        $product2 = ProductFactory::createOne(['companyId' => $companyId]);

        $products = [$product1, $product2];

        $this->productRepository
            ->expects($this->once())
            ->method('paginateByCompanyId')
            ->with($companyId, $page, $perPage)
            ->willReturn($products);

        $this->productRepository
            ->expects($this->once())
            ->method('countByCompanyId')
            ->with($companyId)
            ->willReturn($total);

        // Aseguramos que no se llama a la rama de usuario
        $this->productRepository
            ->expects($this->never())
            ->method('paginateByUserId');
        $this->productRepository
            ->expects($this->never())
            ->method('countByUserId');

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

        $query = new ListProductsByOwnerQuery($companyId, ProductOwner::COMPANY, $page, $perPage);

        /** @var ListProductsByOwnerQueryResult $result */
        $result = ($this->handler)($query);

        $this->assertInstanceOf(ListProductsByOwnerQueryResult::class, $result);

        $responseData = $result->getResponseData();

        $this->assertSame($total, $responseData['total']);
        $this->assertSame($page, $responseData['page']);
        $this->assertSame([$transformed1, $transformed2], $responseData['data']);
    }

    public function test_it_lists_products_by_user_owner_with_pagination(): void
    {
        $userId  = Uuid::v4();
        $page    = 1;
        $perPage = 10;
        $total   = 3;

        /** @var Product $product1 */
        $product1 = ProductFactory::createOne(['userId' => $userId]);
        /** @var Product $product2 */
        $product2 = ProductFactory::createOne(['userId' => $userId]);
        /** @var Product $product3 */
        $product3 = ProductFactory::createOne(['userId' => $userId]);

        $products = [$product1, $product2, $product3];

        $this->productRepository
            ->expects($this->once())
            ->method('paginateByUserId')
            ->with($userId, $page, $perPage)
            ->willReturn($products);

        $this->productRepository
            ->expects($this->once())
            ->method('countByUserId')
            ->with($userId)
            ->willReturn($total);

        // Aseguramos que no se llama a la rama de company
        $this->productRepository
            ->expects($this->never())
            ->method('paginateByCompanyId');
        $this->productRepository
            ->expects($this->never())
            ->method('countByCompanyId');

        $transformed1 = ['id' => (string) $product1->getId(), 'name' => $product1->getName()];
        $transformed2 = ['id' => (string) $product2->getId(), 'name' => $product2->getName()];
        $transformed3 = ['id' => (string) $product3->getId(), 'name' => $product3->getName()];

        $this->productTransformer
            ->expects($this->exactly(3))
            ->method('transform')
            ->withConsecutive(
                [$product1],
                [$product2],
                [$product3]
            )
            ->willReturnOnConsecutiveCalls(
                $transformed1,
                $transformed2,
                $transformed3
            );

        $query = new ListProductsByOwnerQuery($userId, ProductOwner::USER, $page, $perPage);

        /** @var ListProductsByOwnerQueryResult $result */
        $result = ($this->handler)($query);

        $this->assertInstanceOf(ListProductsByOwnerQueryResult::class, $result);

        $responseData = $result->getResponseData();

        $this->assertSame($total, $responseData['total']);
        $this->assertSame($page, $responseData['page']);
        $this->assertSame([$transformed1, $transformed2, $transformed3], $responseData['data']);
    }

    public function test_it_returns_empty_list_for_user_owner_when_no_products_found(): void
    {
        $userId  = Uuid::v4();
        $page    = 1;
        $perPage = 10;
        $total   = 0;

        $this->productRepository
            ->expects($this->once())
            ->method('paginateByUserId')
            ->with($userId, $page, $perPage)
            ->willReturn([]);

        $this->productRepository
            ->expects($this->once())
            ->method('countByUserId')
            ->with($userId)
            ->willReturn($total);

        $this->productRepository
            ->expects($this->never())
            ->method('paginateByCompanyId');
        $this->productRepository
            ->expects($this->never())
            ->method('countByCompanyId');

        $this->productTransformer
            ->expects($this->never())
            ->method('transform');

        $query = new ListProductsByOwnerQuery($userId, ProductOwner::USER, $page, $perPage);

        /** @var ListProductsByOwnerQueryResult $result */
        $result = ($this->handler)($query);

        $this->assertInstanceOf(ListProductsByOwnerQueryResult::class, $result);

        $responseData = $result->getResponseData();

        $this->assertSame($total, $responseData['total']);
        $this->assertSame($page, $responseData['page']);
        $this->assertSame([], $responseData['data']);
    }
}
