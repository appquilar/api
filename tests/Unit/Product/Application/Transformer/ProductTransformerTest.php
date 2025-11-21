<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Transformer;

use App\Product\Application\Transformer\ProductTransformer;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\ValueObject\PublicationStatus;
use App\Tests\Factories\Product\Domain\Entity\ProductFactory;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Uid\Uuid;

class ProductTransformerTest extends UnitTestCase
{
    private ProductTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new ProductTransformer();
    }

    public function test_it_transforms_a_product_entity_into_array(): void
    {
        // Given a fully populated product entity
        /** @var Product $product */
        $product = ProductFactory::createOne([
            'productId' => Uuid::v4(),
            'shortId' => 'short123',
            'internalId' => 'int-999',
            'publicationStatus' => PublicationStatus::published(),
            'description' => 'A sample product',
            'companyId' => Uuid::v4(),
            'categoryId' => Uuid::v4(),
            'imageIds' => [Uuid::v4(), Uuid::v4()],
        ]);

        // WHEN
        $transformed = $this->transformer->transform($product);

        // THEN
        $this->assertSame($product->getId()->toString(), $transformed['id']);
        $this->assertSame($product->getShortId(), $transformed['short_id']);
        $this->assertSame($product->getName(), $transformed['name']);
        $this->assertSame($product->getSlug(), $transformed['slug']);
        $this->assertSame($product->getInternalId(), $transformed['internal_id']);
        $this->assertSame($product->getDescription(), $transformed['description']);
        $this->assertSame($product->getCompanyId()->toString(), $transformed['company_id']);
        $this->assertSame($product->getCategoryId()->toString(), $transformed['category_id']);

        $this->assertCount(2, $transformed['image_ids']);
        $this->assertSame(
            array_map(fn(Uuid $id) => $id->toString(), $product->getImageIds()),
            $transformed['image_ids']
        );

        $this->assertSame($product->getPublicationStatus()->getStatus(), $transformed['publication_status']['status']);
        $this->assertSame(
            $product->getPublicationStatus()->getPublishedAt()?->format('c'),
            $transformed['publication_status']['published_at']
        );

        // Deposit
        $this->assertSame($product->getDeposit()->toArray(), $transformed['deposit']);

        // Tiers
        $this->assertSame($product->getTiers()->toArray(), $transformed['tiers']);
    }

    public function test_it_transforms_product_with_null_company_and_category(): void
    {
        /** @var Product $product */
        $product = ProductFactory::createOne([
            'companyId'  => null,
            'categoryId' => null,
        ]);

        $transformed = $this->transformer->transform($product);

        $this->assertNull($transformed['company_id']);
        $this->assertNull($transformed['category_id']);
    }
}
