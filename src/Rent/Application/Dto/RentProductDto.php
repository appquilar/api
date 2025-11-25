<?php declare(strict_types=1);

namespace App\Rent\Application\Dto;

use App\Rent\Domain\Enum\RentOwnerType;
use Symfony\Component\Uid\Uuid;

class RentProductDto
{
    public function __construct(
        private Uuid $productId,
        private Uuid $ownerId,
        private RentOwnerType $ownerType,
    ) {
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
    }

    public function getOwnerId(): Uuid
    {
        return $this->ownerId;
    }

    public function getOwnerType(): RentOwnerType
    {
        return $this->ownerType;
    }
}
