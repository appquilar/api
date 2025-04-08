<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetProductBySlug;

use App\Shared\Application\Query\Query;

class GetProductBySlugQuery implements Query
{
    public function __construct(
        private string $slug
    ) {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
