<?php declare(strict_types=1);

namespace App\Category\Application\Service;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Shared\Application\Service\SlugifyServiceInterface;
use Symfony\Component\Uid\Uuid;

class GenerateSlugForCategoryService
{
    public function __construct(
        private SlugifyServiceInterface $slugifyService,
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    public function getCategorySlug(string $categoryName, ?Uuid $categoryId = null): string
    {
        $slug = $this->slugifyService->generate($categoryName);
        $this->slugifyService->validateSlugIsUnique($slug, $this->categoryRepository, $categoryId);

        return $slug;
    }
}