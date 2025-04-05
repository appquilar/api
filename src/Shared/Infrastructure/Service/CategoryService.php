<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use Symfony\Component\Uid\Uuid;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function categoryExistsById(Uuid $categoryId): bool
    {
        return $this->categoryRepository->findById($categoryId) !== null;
    }
}
