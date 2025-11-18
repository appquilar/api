<?php declare(strict_types=1);

namespace App\Tests\Unit\Category\Application\Service;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Application\Service\GenerateSlugForCategoryService;
use App\Shared\Application\Service\SlugifyServiceInterface;
use App\Tests\Factories\Category\Domain\Entity\CategoryFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class GenerateSlugForCategoryServiceTest extends UnitTestCase
{
    private CategoryRepositoryInterface|MockObject $categoryRepository;
    private SlugifyServiceInterface|MockObject $slugifyService;
    private GenerateSlugForCategoryService $service;

    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->slugifyService = $this->createMock(SlugifyServiceInterface::class);
        $this->service = new GenerateSlugForCategoryService(
            $this->slugifyService,
            $this->categoryRepository,
        );
    }

    public function testGetCategorySlugTrailWithoutParent(): void
    {
        $inputSlug = 'Cámaras Réflex';

        $this->slugifyService
            ->expects(self::once())
            ->method('generate')
            ->with($inputSlug)
            ->willReturn('camaras-reflex');

        $result = $this->service->getCategorySlug($inputSlug, null);

        self::assertSame('camaras-reflex', $result);
    }
}
