<?php

declare(strict_types=1);

namespace App\Product\Application\Repository;

use App\Product\Domain\Entity\Product;
use App\Shared\Application\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method Product|null findById(Uuid $id)
 * @method Product|null findOneBy(array $criteria)
 */
interface ProductRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Product;
    public function paginateByCompanyId(Uuid $companyId, int $page = 1, int $limit = 10): array;
    public function countByCompanyId(Uuid $companyId): int;

    /**
     * @return Product[]
     */
    public function getProductsByUserId(Uuid $userId): array;
    public function paginateByUserId(Uuid $userId, int $page = 1, int $limit = 10): array;
    public function countByUserId(Uuid $userId): int;

    /**
     * @return Product[]
     */
    public function findByCategoryId(Uuid $categoryId): array;
    public function paginateByCategoryId(array $categoriesId, int $page = 1, int $limit = 10): array;
    public function countByCategoryId(array $categoriesId): int;
}
