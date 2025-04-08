<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetCompanyProductsDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.get_company_products.company_id.not_blank"),
            new Assert\Uuid(message: "product.get_company_products.company_id.uuid"),
        ])]
        public ?Uuid $companyId = null,

        #[Assert\Positive(message: "product.get_company_products.page.positive")]
        public int $page = 1,

        #[Assert\Range(minMessage: "product.get_company_products.per_page.min", maxMessage: "product.get_company_products.per_page.max", min: 1, max: 50)]
        public int $perPage = 10
    ) {
    }
}
