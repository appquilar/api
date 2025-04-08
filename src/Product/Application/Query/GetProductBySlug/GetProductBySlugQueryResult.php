<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetProductBySlug;

use App\Shared\Application\Query\QueryResult;

class GetProductBySlugQueryResult implements QueryResult
{
    public function __construct(
        private array $product
    ) {
    }

    public function getProduct(): array
    {
        return $this->product;
    }
}
