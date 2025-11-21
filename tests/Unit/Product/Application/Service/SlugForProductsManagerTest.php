<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Service;

use App\Product\Application\Service\SlugForProductsManager;
use App\Shared\Application\Service\SlugifyServiceInterface;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class SlugForProductsManagerTest extends UnitTestCase
{
    /** @var SlugifyServiceInterface|MockObject */
    private SlugifyServiceInterface|MockObject $slugifyService;

    private SlugForProductsManager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugifyService = $this->createMock(SlugifyServiceInterface::class);

        $this->manager = new SlugForProductsManager(
            $this->slugifyService
        );
    }

    public function test_it_generates_slug_for_product_using_slugify_service(): void
    {
        $productSlug = 'Mi Producto Súper Chulo';
        $shortId     = 'abc123XYZ';

        $expectedInput  = $productSlug . '-' . $shortId;
        $expectedResult = 'mi-producto-super-chulo-abc123xyz';

        $this->slugifyService
            ->expects($this->once())
            ->method('generate')
            ->with($expectedInput)
            ->willReturn($expectedResult);

        $result = $this->manager->generateSlugForProduct($productSlug, $shortId);

        $this->assertSame($expectedResult, $result);
    }
}
