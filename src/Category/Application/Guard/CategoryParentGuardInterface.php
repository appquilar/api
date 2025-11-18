<?php declare(strict_types=1);

namespace App\Category\Application\Guard;

use App\Category\Domain\Exception\CategoryCantBeItsOwnParentException;
use App\Category\Domain\Exception\CategoryParentCircularException;
use Doctrine\DBAL\Exception;
use Symfony\Component\Uid\Uuid;

interface CategoryParentGuardInterface
{
    /**
     * @throws CategoryCantBeItsOwnParentException
     * @throws CategoryParentCircularException
     * @throws Exception
     */
    public function assertCanAssignParent(Uuid $categoryId, ?Uuid $newParentId = null): void;
}
