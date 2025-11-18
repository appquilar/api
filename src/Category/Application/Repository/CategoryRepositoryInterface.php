<?php

declare(strict_types=1);

namespace App\Category\Application\Repository;

use App\Category\Domain\Entity\Category;
use App\Category\Domain\Exception\CategoryParentCircularException;
use App\Category\Domain\ValueObject\CategoryPathValueObject;
use App\Shared\Application\Repository\RepositoryInterface;
use Doctrine\DBAL\Exception;
use Symfony\Component\Uid\Uuid;

/**
 * @method Category|null findById(Uuid $id)
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Category;

    /**
     * @param Uuid $rootCategoryId
     * @param int $maxDepth
     * @return Category[]
     */
    public function getSubtreeIncludingSelf(Uuid $rootCategoryId, int $maxDepth = 5): array;

    /**
     * @throws CategoryParentCircularException|Exception
     */
    public function getParentsFromCategory(Uuid $categoryId, int $maxDepth = 20): CategoryPathValueObject;
}
