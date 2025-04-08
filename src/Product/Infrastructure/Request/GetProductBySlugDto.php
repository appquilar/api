<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GetProductBySlugDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.get_by_slug.slug.not_blank"),
        ])]
        public ?string $slug = null
    ) {
    }
}
