<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategoryById;

use App\Shared\Application\Query\QueryResult;

class GetCategoryByIdQueryResult implements QueryResult
{
    public function __construct(
        private array $category,
    ) {
    }

    public function getCategory(): array
    {
        return $this->category;
    }
}
