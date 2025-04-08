<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetCompanyProducts;

use App\Shared\Application\Query\PaginatedQuery;
use Symfony\Component\Uid\Uuid;

class GetCompanyProductsQuery extends PaginatedQuery
{
    public function __construct(
        private Uuid $companyId,
        int $page = 1,
        int $perPage = 10
    ) {
        parent::__construct($page, $perPage);
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }
}
