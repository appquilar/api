<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetCategoryBySlugDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "category.get_by_slug.slug.not_blank"),
        ])]
        public ?string $slug = null,
    ) {
    }
}
