<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Request;

use App\Product\Infrastructure\Request\Input\TierInput;
use App\Shared\Infrastructure\Request\Constraint\CategoryExists;
use App\Shared\Infrastructure\Request\Constraint\ImageExists;
use App\Shared\Infrastructure\Request\Input\MoneyInput;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateProductDto implements RequestDtoInterface
{
    /**
     * @param Uuid|null $productId
     * @param string|null $name
     * @param string|null $internalId
     * @param string|null $description
     * @param int|null $quantity
     * @param Uuid|null $companyId
     * @param Uuid|null $categoryId
     * @param Uuid[]|null $imageIds
     * @param MoneyInput|null $deposit
     * @param TierInput[]|null $tiers
     */
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.create.product_id.not_blank"),
            new Assert\Uuid(message: "product.create.product_id.uuid"),
        ])]
        public ?Uuid $productId = null,

        #[Assert\NotBlank(message: "product.create.name.not_blank")]
        public ?string $name = null,

        #[Assert\NotBlank(message: "product.create.internal_id.not_blank")]
        public ?string $internalId = null,

        public ?string $description = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.create.quantity.not_blank"),
                new Assert\GreaterThan(value: 0, message: "product.create.quantity.uuid"),
            ]),
            new Assert\IsNull()
        ])]
        public ?int $quantity = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.create.company_id.not_blank"),
                new Assert\Uuid(message: "product.create.company_id.uuid"),
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $companyId = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.create.category_id.not_blank"),
                new Assert\Uuid(message: "product.create.category_id.uuid"),
                new CategoryExists(message: "product.create.category_id.exists")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $categoryId = null,

        #[Assert\All([
            new Assert\Sequentially([
                new Assert\Uuid(message: "product.create.image_id.uuid"),
                new ImageExists(message: "product.create.image_id.exists")
            ])
        ])]
        public ?array $imageIds = [],

        #[Assert\NotNull(message: "product.create.deposit.money.not_null")]
        #[Assert\Valid]
        public ?MoneyInput $deposit = null,

        #[Assert\NotNull(message: "product.create.tiers.not_null")]
        #[Assert\Count(min: 1, minMessage: 'product.tiers.at_least_one')]
        #[Assert\Valid]
        public ?array $tiers = null,
    ) {
    }
}
