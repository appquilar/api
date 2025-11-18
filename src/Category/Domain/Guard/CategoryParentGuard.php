<?php declare(strict_types=1);

namespace App\Category\Domain\Guard;

use App\Category\Application\Guard\CategoryParentGuardInterface;
use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Domain\Exception\CategoryCantBeItsOwnParentException;
use App\Category\Domain\Exception\CategoryParentCircularException;
use Doctrine\DBAL\Exception;
use Symfony\Component\Uid\Uuid;

class CategoryParentGuard implements CategoryParentGuardInterface
{

    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @throws CategoryCantBeItsOwnParentException
     * @throws CategoryParentCircularException
     * @throws Exception
     */
    public function assertCanAssignParent(Uuid $categoryId, ?Uuid $newParentId = null): void
    {
        if ($newParentId === null) {
            return;
        }

        if ($categoryId->equals($newParentId)) {
            throw new CategoryCantBeItsOwnParentException('category.update.parent_id.own_id_as_parent');
        }

        // If there's a circular relation, this will throw an exception
        $path = $this->categoryRepository->getParentsFromCategory($newParentId);

        if ($path->containsCategory($categoryId)) {
            throw new CategoryParentCircularException('category.update.parent_id.circular');
        }
    }
}
