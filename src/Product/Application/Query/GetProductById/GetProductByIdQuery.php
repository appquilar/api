<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetProductById;

use App\Shared\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

class GetProductByIdQuery implements Query
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
