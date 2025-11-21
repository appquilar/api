<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Query\SearchProducts;

use App\Product\Application\Adapter\ProductSearchAdapterInterface;
use App\Product\Application\Dto\ProductSearchHitDto;
use App\Product\Application\Query\SearchProducts\SearchProductsQuery;
use App\Product\Application\Query\SearchProducts\SearchProductsQueryHandler;
use App\Product\Application\Query\SearchProducts\SearchProductsQueryResult;
use App\Product\Application\Transformer\ProductHitTransformer;
use App\Product\Domain\Enum\ProductOwner;
use App\Product\Domain\ValueObject\ProductCategoryPathValueObject;
use App\Product\Domain\ValueObject\PublicationStatus;
use App\Shared\Domain\ValueObject\GeoLocation;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

final class SearchProductsQueryHandlerTest extends UnitTestCase
{
    /** @var ProductSearchAdapterInterface|MockObject */
    private ProductSearchAdapterInterface|MockObject $productSearchAdapter;

    /** @var ProductHitTransformer|MockObject */
    private ProductHitTransformer|MockObject $productHitTransformer;

    private SearchProductsQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productSearchAdapter   = $this->createMock(ProductSearchAdapterInterface::class);
        $this->productHitTransformer  = $this->createMock(ProductHitTransformer::class);

        $this->handler = new SearchProductsQueryHandler(
            $this->productSearchAdapter,
            $this->productHitTransformer
        );
    }

    public function test_it_searches_products_and_returns_paginated_result(): void
    {
        $text              = 'taladro';
        $latitude          = 41.5381;
        $longitude         = 2.4445;
        $radiusKm          = 10;
        $categoryIds       = [Uuid::v4(), Uuid::v4()];
        $publicationStatus = PublicationStatus::published();
        $page              = 2;
        $perPage           = 20;

        $query = new SearchProductsQuery(
            $text,
            $latitude,
            $longitude,
            $radiusKm,
            $publicationStatus,
            $categoryIds,
            $page,
            $perPage
        );

        $categoriesArray1 = [[
            'id'          => $categoryIds[0]->toString(),
            'slug'        => 'category-1',
            'name'        => 'Category 1',
            'description' => 'Category 1 description',
            'parent_id'   => null,
        ]];

        $categoriesArray2 = [[
            'id'          => $categoryIds[1]->toString(),
            'slug'        => 'category-2',
            'name'        => 'Category 2',
            'description' => 'Category 2 description',
            'parent_id'   => null,
        ]];

        $hit1 = new ProductSearchHitDto(
            Uuid::v4(),
            'Taladro Bosch',
            'taladro-bosch',
            'Taladro Bosch description',
            new GeoLocation($latitude, $longitude),
            ProductCategoryPathValueObject::fromArray($categoriesArray1),
            PublicationStatus::published(),
            Uuid::v4(),
            ProductOwner::USER,
            [] // imageIds
        );

        $hit2 = new ProductSearchHitDto(
            Uuid::v4(),
            'Taladro Makita',
            'taladro-makita',
            'Taladro Makita description',
            new GeoLocation($latitude, $longitude),
            ProductCategoryPathValueObject::fromArray($categoriesArray2),
            PublicationStatus::published(),
            Uuid::v4(),
            ProductOwner::USER,
            []
        );

        $expectedTotal = 42;

        $this->productSearchAdapter
            ->expects($this->once())
            ->method('search')
            ->with(
                $publicationStatus,
                $text,
                $latitude,
                $longitude,
                $radiusKm,
                $categoryIds,
                $page,
                $perPage
            )
            ->willReturn([
                'items' => [$hit1, $hit2],
                'total' => $expectedTotal,
            ]);

        $transformedHit1 = ['id' => $hit1->getId()->toString(), 'name' => $hit1->getName()];
        $transformedHit2 = ['id' => $hit2->getId()->toString(), 'name' => $hit2->getName()];

        $this->productHitTransformer
            ->expects($this->exactly(2))
            ->method('transform')
            ->withConsecutive(
                [
                    $this->identicalTo($hit1),
                    $this->callback(function ($origin) use ($latitude, $longitude) {
                        $this->assertInstanceOf(GeoLocation::class, $origin);
                        $this->assertSame($latitude, $origin->getLatitude());
                        $this->assertSame($longitude, $origin->getLongitude());

                        return true;
                    }),
                ],
                [
                    $this->identicalTo($hit2),
                    $this->callback(function ($origin) use ($latitude, $longitude) {
                        $this->assertInstanceOf(GeoLocation::class, $origin);
                        $this->assertSame($latitude, $origin->getLatitude());
                        $this->assertSame($longitude, $origin->getLongitude());

                        return true;
                    }),
                ],
            )
            ->willReturnOnConsecutiveCalls(
                $transformedHit1,
                $transformedHit2
            );

        /** @var SearchProductsQueryResult $result */
        $result = ($this->handler)($query);

        $this->assertInstanceOf(SearchProductsQueryResult::class, $result);

        $responseData = $result->getResponseData();

        $this->assertSame($expectedTotal, $responseData['total']);
        $this->assertSame($page, $responseData['page']);
        $this->assertSame(
            [$transformedHit1, $transformedHit2],
            $responseData['data']
        );
    }

    public function test_it_returns_empty_result_when_adapter_returns_no_items(): void
    {
        $text              = null;
        $latitude          = null;
        $longitude         = null;
        $radiusKm          = null;
        $categoryIds       = [];
        $publicationStatus = PublicationStatus::published();
        $page              = 1;
        $perPage           = 10;

        $query = new SearchProductsQuery(
            $text,
            $latitude,
            $longitude,
            $radiusKm,
            $publicationStatus,
            $categoryIds,
            $page,
            $perPage
        );

        $this->productSearchAdapter
            ->expects($this->once())
            ->method('search')
            ->with(
                $publicationStatus,
                $text,
                $latitude,
                $longitude,
                $radiusKm,
                $categoryIds,
                $page,
                $perPage
            )
            ->willReturn([
                'items' => [],
                'total' => 0,
            ]);

        $this->productHitTransformer
            ->expects($this->never())
            ->method('transform');

        /** @var SearchProductsQueryResult $result */
        $result = ($this->handler)($query);

        $this->assertInstanceOf(SearchProductsQueryResult::class, $result);

        $responseData = $result->getResponseData();

        $this->assertSame(0, $responseData['total']);
        $this->assertSame($page, $responseData['page']);
        $this->assertSame([], $responseData['data']);
    }
}
