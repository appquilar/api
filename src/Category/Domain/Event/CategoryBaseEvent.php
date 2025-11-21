<?php declare(strict_types=1);

namespace App\Category\Domain\Event;

use Symfony\Component\Uid\Uuid;

abstract class CategoryBaseEvent
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
