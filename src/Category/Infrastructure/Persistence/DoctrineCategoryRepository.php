<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Persistence;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Domain\Entity\Category;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @method Category|null findById(Uuid $id)
 */
class DoctrineCategoryRepository extends DoctrineRepository implements CategoryRepositoryInterface
{
    public function getClass(): string
    {
        return Category::class;
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->findOneBy(['slug' => $slug]);
    }
}
