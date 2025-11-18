<?php declare(strict_types=1);

namespace App\Product\Application\Query\ListProductsByCategoryId;

use App\Shared\Application\Query\PaginatedQuery;
use Symfony\Component\Uid\Uuid;

class ListProductsByCategoryIdQuery extends PaginatedQuery
{
    public function __construct(
        private Uuid $categoryId,
        int $page = 1,
        int $perPage = 10,
    ) {
        parent::__construct($page, $perPage);
    }

    public function getCategoryId(): Uuid
    {
        return $this->categoryId;
    }
}
