<?php

declare(strict_types=1);

namespace App\Company\Application\Query\GetCompanyBySlug;

use App\Shared\Application\Query\Query;

class GetCompanyBySlugQuery implements Query
{
    public function __construct(
        private string $slug,
    ) {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
