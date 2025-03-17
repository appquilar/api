<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategoryBySlug;

use App\Shared\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

class GetCategoryBySlugQuery implements Query
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
