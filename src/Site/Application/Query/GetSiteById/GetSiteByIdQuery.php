<?php

declare(strict_types=1);

namespace App\Site\Application\Query\GetSiteById;

use App\Shared\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

class GetSiteByIdQuery implements Query
{
    public function __construct(
        private Uuid $siteId
    ) {
    }

    public function getSiteId(): Uuid
    {
        return $this->siteId;
    }
}
