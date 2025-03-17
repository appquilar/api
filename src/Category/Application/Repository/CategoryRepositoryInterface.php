<?php

declare(strict_types=1);

namespace App\Category\Application\Repository;

use App\Category\Domain\Entity\Category;
use App\Shared\Application\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method Category|null findById(Uuid $id)
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Category;
}
