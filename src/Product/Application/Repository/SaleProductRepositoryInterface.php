<?php

declare(strict_types=1);

namespace App\Product\Application\Repository;

use App\Product\Domain\Entity\SaleProduct;
use App\Shared\Application\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method SaleProduct|null findById(Uuid $id)
 * @method SaleProduct|null findOneBy(array $criteria)
 */
interface SaleProductRepositoryInterface extends RepositoryInterface
{
    public function findByProductId(Uuid $productId): ?SaleProduct;
}
