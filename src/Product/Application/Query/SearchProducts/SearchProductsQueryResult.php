<?php declare(strict_types=1);

namespace App\Product\Application\Query\SearchProducts;

use App\Shared\Application\Query\PaginatedQueryResult;

class SearchProductsQueryResult extends PaginatedQueryResult
{
    public function __construct(
        private array $products,
        int $total,
        int $page
    ) {
        parent::__construct($total, $page);
    }

    function getData(): array
    {
        return $this->products;
    }
}
