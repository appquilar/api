<?php

declare(strict_types=1);

namespace App\Shared\Application\Query;

abstract class PaginatedQuery implements Query
{
    public function __construct(
        private int $page = 1,
        private int $perPage = 10,
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }
}
