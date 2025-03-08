<?php

declare(strict_types=1);

namespace App\Shared\Application\Query;

abstract class PaginatedQueryResult implements QueryResult
{
    public function __construct(
        private int $total,
        private int $page,
    ) {
    }

    abstract function getData(): array;

    public function getResponseData(): array
    {
        return [
            'total' => $this->total,
            'page' => $this->page,
            'data' => $this->getData(),
        ];
    }
}
