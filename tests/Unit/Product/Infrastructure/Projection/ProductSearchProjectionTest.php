<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Infrastructure\Projection;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductCategoryServiceInterface;
use App\Product\Application\Service\ProductCompanyServiceInterface;
use App\Product\Application\Service\ProductSearchIndexerInterface;
use App\Product\Application\Service\ProductUserServiceInterface;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Enum\ProductOwner;
use App\Product\Domain\ValueObject\ProductCategoryPathValueObject;
use App\Product\Domain\ValueObject\PublicationStatus;
use App\Product\Infrastructure\Persistence\ProductSearchRepositoryInterface;
use App\Product\Infrastructure\Projection\ProductSearchProjection;
use App\Product\Infrastructure\ReadModel\ProductSearch\ProductSearchReadModel;
use App\Shared\Domain\ValueObject\GeoLocation;
use App\Tests\Unit\UnitTestCase;
use App\Product\Domain\Dto\ProductCategoryPathItemDto;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class ProductSearchProjectionTest extends UnitTestCase
{
    /** @var ProductRepositoryInterface|MockObject */
    private ProductRepositoryInterface|MockObject $productRepository;

    /** @var ProductSearchRepositoryInterface|MockObject */
    private ProductSearchRepositoryInterface|MockObject $productSearchRepository;

    /** @var ProductCategoryServiceInterface|MockObject */
    private ProductCategoryServiceInterface|MockObject $productCategoryService;

    /** @var ProductUserServiceInterface|MockObject */
    private ProductUserServiceInterface|MockObject $productUserService;

    /** @var ProductCompanyServiceInterface|MockObject */
    private ProductCompanyServiceInterface|MockObject $productCompanyService;

    /** @var ProductSearchIndexerInterface|MockObject */
    private ProductSearchIndexerInterface|MockObject $indexer;

    private ProductSearchProjection $projection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository       = $this->createMock(ProductRepositoryInterface::class);
        $this->productSearchRepository = $this->createMock(ProductSearchRepositoryInterface::class);
        $this->productCategoryService  = $this->createMock(ProductCategoryServiceInterface::class);
        $this->productUserService      = $this->createMock(ProductUserServiceInterface::class);
        $this->productCompanyService   = $this->createMock(ProductCompanyServiceInterface::class);
        $this->indexer                 = $this->createMock(ProductSearchIndexerInterface::class);

        $this->projection = new ProductSearchProjection(
            $this->productRepository,
            $this->productSearchRepository,
            $this->productCategoryService,
            $this->productUserService,
            $this->productCompanyService,
            $this->indexer
        );
    }

    public function test_sync_when_product_event_does_nothing_if_product_not_found(): void
    {
        $productId = Uuid::v4();

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn(null);

        $this->productSearchRepository
            ->expects($this->never())
            ->method('save');

        $this->indexer
            ->expects($this->never())
            ->method('index');

        $this->projection->syncWhenProductEvent($productId);
    }

    public function test_sync_when_product_event_creates_new_read_model_for_user_owner(): void
    {
        $productId = Uuid::v4();
        $userId    = Uuid::v4();
        $categoryId = Uuid::v4();

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn($productId);
        $product->method('getName')->willReturn('Taladro Bosch');
        $product->method('getDescription')->willReturn('Un taladro muy potente');
        $product->method('belongsToUser')->willReturn(true);
        $product->method('getUserId')->willReturn($userId);
        $product->method('getCategoryId')->willReturn($categoryId);
        $product->method('getPublicationStatus')->willReturn(PublicationStatus::published());

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($product);

        // Localización del usuario
        /** @var GeoLocation|MockObject $location */
        $location = $this->createMock(GeoLocation::class);
        $location->method('getLatitude')->willReturn(41.5381);
        $location->method('getLongitude')->willReturn(2.4445);
        $location->method('generateCircle')->willReturn(['circle-data']);

        $this->productUserService
            ->expects($this->once())
            ->method('getUserLocationByUserId')
            ->with($userId)
            ->willReturn($location);

        $this->productCompanyService
            ->expects($this->never())
            ->method('getCompanyLocationByCompanyId');

        // Categorías
        /** @var ProductCategoryPathItemDto|MockObject $item */
        $item = $this->createMock(ProductCategoryPathItemDto::class);
        $item->method('toArray')->willReturn(['id' => (string) $categoryId, 'name' => 'Herramientas']);

        $categoriesVO = new ProductCategoryPathValueObject([$item]);

        $this->productCategoryService
            ->expects($this->once())
            ->method('getParentsFromCategory')
            ->with($categoryId)
            ->willReturn($categoriesVO);

        // No existe todavía un read model
        $this->productSearchRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn(null);

        $this->productSearchRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(ProductSearchReadModel::class));

        $this->indexer
            ->expects($this->once())
            ->method('index')
            ->with(
                $productId->toString(),
                $this->isType('array')
            );

        $this->projection->syncWhenProductEvent($productId);
    }

    public function test_sync_when_product_event_updates_existing_read_model_for_company_owner(): void
    {
        $productId  = Uuid::v4();
        $companyId  = Uuid::v4();

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn($productId);
        $product->method('getName')->willReturn('Taladro Makita');
        $product->method('getDescription')->willReturn('Taladro profesional');
        $product->method('belongsToUser')->willReturn(false);
        $product->method('belongsToCompany')->willReturn(true);
        $product->method('getCompanyId')->willReturn($companyId);
        $product->method('getCategoryId')->willReturn(null);
        $product->method('getPublicationStatus')->willReturn(PublicationStatus::default());

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($product);

        /** @var GeoLocation|MockObject $location */
        $location = $this->createMock(GeoLocation::class);
        $location->method('getLatitude')->willReturn(41.39);
        $location->method('getLongitude')->willReturn(2.15);
        $location->method('generateCircle')->willReturn([]);

        $this->productUserService
            ->expects($this->never())
            ->method('getUserLocationByUserId');

        $this->productCompanyService
            ->expects($this->once())
            ->method('getCompanyLocationByCompanyId')
            ->with($companyId)
            ->willReturn($location);

        $this->productCategoryService
            ->expects($this->never())
            ->method('getParentsFromCategory');

        $existingReadModel = new ProductSearchReadModel(
            $productId,
            'Old name',
            'old-name',
            'Old desc',
            41.39,
            2.15,
            [],
            [],
            PublicationStatus::STATUS_DRAFT,
            $companyId,
            ProductOwner::COMPANY->value,
            []
        );

        $this->productSearchRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($existingReadModel);

        $this->productSearchRepository
            ->expects($this->once())
            ->method('save')
            ->with($existingReadModel);

        $this->indexer
            ->expects($this->once())
            ->method('index')
            ->with(
                $productId->toString(),
                $this->isType('array')
            );

        $this->projection->syncWhenProductEvent($productId);
    }

    public function test_sync_when_category_event_syncs_all_products_for_category_trail(): void
    {
        $rootCategoryId = Uuid::v4();
        $categoryId1    = Uuid::v4();
        $categoryId2    = Uuid::v4();

        // Items del path
        /** @var ProductCategoryPathItemDto|MockObject $item1 */
        $item1 = $this->createMock(ProductCategoryPathItemDto::class);
        $item1->method('getId')->willReturn($categoryId1);

        /** @var ProductCategoryPathItemDto|MockObject $item2 */
        $item2 = $this->createMock(ProductCategoryPathItemDto::class);
        $item2->method('getId')->willReturn($categoryId2);

        $categoriesVO = new ProductCategoryPathValueObject([$item1, $item2]);

        $this->productCategoryService
            ->expects($this->once())
            ->method('getParentsFromCategory')
            ->with($rootCategoryId)
            ->willReturn($categoriesVO);

        // 3 productos, uno repetido en ambas categorías
        $productId1 = Uuid::v4();
        $productId2 = Uuid::v4();
        $productId3 = Uuid::v4();

        /** @var Product|MockObject $product1 */
        $product1 = $this->createMock(Product::class);
        $product1->method('getId')->willReturn($productId1);
        $product1->method('belongsToUser')->willReturn(true);
        $product1->method('getUserId')->willReturn(Uuid::v4());
        $product1->method('getCategoryId')->willReturn(null);
        $product1->method('getPublicationStatus')->willReturn(PublicationStatus::default());

        /** @var Product|MockObject $product2 */
        $product2 = $this->createMock(Product::class);
        $product2->method('getId')->willReturn($productId2);
        $product2->method('belongsToUser')->willReturn(true);
        $product2->method('getUserId')->willReturn(Uuid::v4());
        $product2->method('getCategoryId')->willReturn(null);
        $product2->method('getPublicationStatus')->willReturn(PublicationStatus::default());

        /** @var Product|MockObject $product3Shared */
        $product3Shared = $this->createMock(Product::class);
        $product3Shared->method('getId')->willReturn($productId3);
        $product3Shared->method('belongsToUser')->willReturn(true);
        $product3Shared->method('getUserId')->willReturn(Uuid::v4());
        $product3Shared->method('getCategoryId')->willReturn(null);
        $product3Shared->method('getPublicationStatus')->willReturn(PublicationStatus::default());

        $this->productRepository
            ->expects($this->exactly(2))
            ->method('findByCategoryId')
            ->withConsecutive(
                [$categoryId1],
                [$categoryId2]
            )
            ->willReturnOnConsecutiveCalls(
                [$product1, $product3Shared],
                [$product3Shared, $product2]
            );

        // Localización sencilla para todos los productos (user owner)
        /** @var GeoLocation|MockObject $location */
        $location = $this->createMock(GeoLocation::class);
        $location->method('getLatitude')->willReturn(41.5);
        $location->method('getLongitude')->willReturn(2.4);
        $location->method('generateCircle')->willReturn(['circle']);

        $this->productUserService
            ->method('getUserLocationByUserId')
            ->willReturn($location);

        $this->productCompanyService
            ->expects($this->never())
            ->method('getCompanyLocationByCompanyId');

        // Para todos estos productos, no hay categoría -> path vacío
        $this->productCategoryService
            ->expects($this->once())
            ->method('getParentsFromCategory')
            ->with($rootCategoryId)
            ->willReturn($categoriesVO);

        // No read model previo → se crean nuevos
        $this->productSearchRepository
            ->expects($this->exactly(3))
            ->method('findById')
            ->willReturn(null);

        $this->productSearchRepository
            ->expects($this->exactly(3))
            ->method('save')
            ->with($this->isInstanceOf(ProductSearchReadModel::class));

        $this->indexer
            ->expects($this->exactly(3))
            ->method('index')
            ->with(
                $this->isType('string'),
                $this->isType('array')
            );

        $this->projection->syncWhenCategoryEvent($rootCategoryId);
    }
}
