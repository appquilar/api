<?php declare(strict_types=1);

namespace App\Category\Application\Query\GetCategoryBreadcrumbs;

use App\Shared\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

class GetCategoryBreadcrumbsQuery implements Query
{
    public function __construct(
        private readonly Uuid $categoryId
    ) {
    }

    public function getCategoryId(): Uuid
    {
        return $this->categoryId;
    }
}
