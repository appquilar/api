<?php

declare(strict_types=1);

namespace App\Company\Application\Query\GetCompanyById;

use App\Shared\Application\Query\QueryResult;

class GetCompanyByIdQueryResult implements QueryResult
{
    public function __construct(
        private array $company
    ) {
    }

    public function getCompany(): array
    {
        return $this->company;
    }
}
