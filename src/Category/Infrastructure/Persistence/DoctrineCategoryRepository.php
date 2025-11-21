<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Persistence;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Domain\Entity\Category;
use App\Category\Domain\Exception\CategoryParentCircularException;
use App\Category\Domain\ValueObject\CategoryPathValueObject;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use Doctrine\DBAL\Exception;
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

    /**
     * @param Uuid $rootCategoryId
     * @param int $maxDepth
     * @return Uuid[]
     * @throws Exception
     */
    public function getSubtreeIncludingSelf(Uuid $rootCategoryId, int $maxDepth = 5): array
    {
        $sql = <<<SQL
WITH RECURSIVE category_tree AS (
    SELECT 
        c.id,
        c.parent_id,
        0 AS depth
    FROM categories c
    WHERE c.id = :parent_id

    UNION ALL

    SELECT 
        c2.id,
        c2.parent_id,
        ct.depth + 1 AS depth
    FROM categories c2
    JOIN category_tree ct ON c2.parent_id = ct.id
    WHERE ct.depth < :max_depth
)
SELECT 
    id,
    parent_id,
    depth
FROM category_tree
ORDER BY depth ASC;
SQL;

        $rows = $this->entityManager->getConnection()->fetchAllAssociative(
            $sql,
            [
                'parent_id' => $rootCategoryId->toBinary(),
                'max_depth' => $maxDepth,
            ],
        );

        return array_map(
            static fn (array $row): Uuid => Uuid::fromBinary($row['id']),
            $rows
        );
    }

    /**
     * @throws CategoryParentCircularException|Exception
     */
    public function getParentsFromCategory(Uuid $categoryId, int $maxDepth = 20): CategoryPathValueObject
    {
        $sql = <<<SQL
WITH RECURSIVE category_path AS (
    SELECT 
        c.id,
        c.parent_id,
        c.slug,
        c.name,
        c.description,
        c.icon_id,
        0 AS depth
    FROM categories c
    WHERE c.id = :category_id

    UNION ALL

    SELECT 
        c2.id,
        c2.parent_id,
        c2.slug,
        c2.name,
        c2.description,
        c2.icon_id,
        cp.depth + 1 AS depth
    FROM categories c2
    JOIN category_path cp ON cp.parent_id = c2.id
    WHERE cp.depth < :max_depth
)
SELECT 
    id,
    parent_id,
    slug,
    name,
    description,
    icon_id,
    depth
FROM category_path;
SQL;

        $rows = $this->entityManager->getConnection()->fetchAllAssociative(
            $sql,
            [
                'category_id' => $categoryId->toBinary(),
                'max_depth'   => $maxDepth,
            ],
        );

        return CategoryPathValueObject::fromItems($rows);
    }
}
