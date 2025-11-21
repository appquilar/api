<?php declare(strict_types=1);

namespace App\Product\Domain\Event;

use Symfony\Component\Uid\Uuid;

abstract class ProductBaseEvent
{
    public function __construct(
        private Uuid $productId
    ) {
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
    }
}
