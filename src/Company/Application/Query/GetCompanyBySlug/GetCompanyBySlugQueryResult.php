<?php

declare(strict_types=1);

namespace App\Company\Application\Query\GetCompanyBySlug;

use App\Shared\Application\Query\QueryResult;

class GetCompanyBySlugQueryResult implements QueryResult
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
