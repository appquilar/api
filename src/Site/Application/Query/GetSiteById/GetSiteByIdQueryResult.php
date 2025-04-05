<?php

declare(strict_types=1);

namespace App\Site\Application\Query\GetSiteById;

use App\Shared\Application\Query\QueryResult;

class GetSiteByIdQueryResult implements QueryResult
{
    public function __construct(
        private array $site
    ) {
    }

    public function getSite(): array
    {
        return $this->site;
    }
}
