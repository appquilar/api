<?php declare(strict_types=1);

namespace App\Category\Application\Query\GetCategoryBreadcrumbs;

use App\Shared\Application\Query\QueryResult;

class GetCategoryBreadcrumbsQueryResult implements QueryResult
{
    public function __construct(
        private array $breadcrumbs
    ) {
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
}
