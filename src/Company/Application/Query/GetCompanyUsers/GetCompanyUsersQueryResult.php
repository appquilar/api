<?php

declare(strict_types=1);

namespace App\Company\Application\Query\GetCompanyUsers;

use App\Shared\Application\Query\PaginatedQueryResult;

class GetCompanyUsersQueryResult extends PaginatedQueryResult
{
    public function __construct(
        private array $data,
        int $total,
        int $page
    ) {
        parent::__construct($total, $page);
    }

    public function getData(): array
    {
        return $this->data;
    }
}
