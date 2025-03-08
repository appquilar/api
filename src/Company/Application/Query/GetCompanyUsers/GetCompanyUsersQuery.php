<?php

declare(strict_types=1);

namespace App\Company\Application\Query\GetCompanyUsers;

use App\Shared\Application\Query\PaginatedQuery;
use Symfony\Component\Uid\Uuid;

class GetCompanyUsersQuery extends PaginatedQuery
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
