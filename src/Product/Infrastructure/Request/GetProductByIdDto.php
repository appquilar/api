<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetProductByIdDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.get_by_id.product_id.not_blank"),
            new Assert\Uuid(message: "product.get_by_id.product_id.uuid"),
        ])]
        public ?Uuid $productId = null
    ) {
    }
}
