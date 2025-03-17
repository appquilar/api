<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategoryById;

use App\Shared\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

class GetCategoryByIdQuery implements Query
{
    public function __construct(
        private Uuid $categoryId
    ) {
    }

    public function getCategoryId(): Uuid
    {
        return $this->categoryId;
    }
}
