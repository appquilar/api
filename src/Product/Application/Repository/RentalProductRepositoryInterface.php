<?php

declare(strict_types=1);

namespace App\Product\Application\Repository;

use App\Product\Domain\Entity\RentalProduct;
use App\Shared\Application\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method RentalProduct|null findById(Uuid $id)
 * @method RentalProduct|null findOneBy(array $criteria)
 */
interface RentalProductRepositoryInterface extends RepositoryInterface
{
    public function findByProductId(Uuid $productId): ?RentalProduct;
}
