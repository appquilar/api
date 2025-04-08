<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Application\Repository\SaleProductRepositoryInterface;
use App\Product\Domain\Entity\SaleProduct;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

class SaleProductRepository extends DoctrineRepository implements SaleProductRepositoryInterface
{
    public function getClass(): string
    {
        return SaleProduct::class;
    }

    public function findByProductId(Uuid $productId): ?SaleProduct
    {
        return $this->findOneBy(['product' => $productId]);
    }
}
