<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Application\Repository\RentalProductRepositoryInterface;
use App\Product\Domain\Entity\RentalProduct;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

class RentalProductRepository extends DoctrineRepository implements RentalProductRepositoryInterface
{
    public function getClass(): string
    {
        return RentalProduct::class;
    }

    public function findByProductId(Uuid $productId): ?RentalProduct
    {
        return $this->findOneBy(['product' => $productId]);
    }
}
