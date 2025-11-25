<?php declare(strict_types=1);

namespace App\Rent\Application\Query\ListRents;

use App\Shared\Application\Query\PaginatedQueryResult;

class ListRentsQueryResult extends PaginatedQueryResult
{
    public function __construct(
        private array $items,
        int $total,
        int $page,
    ) {
        parent::__construct($total, $page);
    }

    function getData(): array
    {
        return $this->items;
    }
}
