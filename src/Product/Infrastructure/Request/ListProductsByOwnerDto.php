<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class ListProductsByOwnerDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.get_owner_products.owner_id.not_blank"),
            new Assert\Uuid(message: "product.get_owner_products.owner_id.uuid"),
        ])]
        public ?Uuid $ownerId = null,
        #[Assert\Positive(message: "product.get_owner_products.page.positive")]
        public int $page = 1,

        #[Assert\Positive(message: "product.get_owner_products.per_page.positive")]
        public int $perPage = 10
    ) {
    }
}
