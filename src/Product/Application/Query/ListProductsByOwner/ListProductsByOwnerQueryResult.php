<?php declare(strict_types=1);

namespace App\Product\Application\Query\ListProductsByOwner;

use App\Shared\Application\Query\PaginatedQueryResult;

class ListProductsByOwnerQueryResult extends PaginatedQueryResult
{
    public function __construct(
        private array $products,
        int $total,
        int $page
    ) {
        parent::__construct($total, $page);
    }

    public function getData(): array
    {
        return $this->products;
    }
}
