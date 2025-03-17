<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetCategoryByIdDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "category.get_by_id.category_id.not_blank"),
            new Assert\Uuid(message: "category.get_by_id.category_id.uuid"),
        ])]
        public ?Uuid $categoryId = null,
    ) {
    }
}
