<?php

declare(strict_types=1);

namespace App\Site\Application\Query\ListSites;

use App\Shared\Application\Query\QueryResult;

class ListSitesQueryResult implements QueryResult
{
    public function __construct(
        private array $sites
    ) {
    }

    public function getSites(): array
    {
        return $this->sites;
    }
}
