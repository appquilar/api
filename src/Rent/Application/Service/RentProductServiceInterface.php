<?php declare(strict_types=1);

namespace App\Rent\Application\Service;

use App\Rent\Application\Dto\RentProductDto;
use Symfony\Component\Uid\Uuid;

interface RentProductServiceInterface
{
    public function getProductOwnershipByProductId(Uuid $productId): ?RentProductDto;
}
