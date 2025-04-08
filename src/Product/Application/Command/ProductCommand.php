<?php

declare(strict_types=1);

namespace App\Product\Application\Command;

use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

abstract class ProductCommand implements Command
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
