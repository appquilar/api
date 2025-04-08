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
    public function findByCompanyId(Uuid $companyId, int $page = 1, int $limit = 10): array;
    public function countByCompanyId(Uuid $companyId): int;
}
