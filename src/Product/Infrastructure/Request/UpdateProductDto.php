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

class UpdateProductDto implements RequestDtoInterface
{
    /**
     * @param Uuid|null $productId
     * @param string|null $name
     * @param string|null $internalId
     * @param string|null $description
     * @param int|null $quantity
     * @param Uuid|null $categoryId
     * @param array|null $imageIds
     * @param MoneyInput|null $deposit
     * @param TierInput[]|null $tiers
     */
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.update.product_id.not_blank"),
            new Assert\Uuid(message: "product.update.product_id.uuid"),
        ])]
        public ?Uuid $productId = null,

        #[Assert\NotBlank(message: "product.update.name.not_blank")]
        public ?string $name = null,

        #[Assert\NotBlank(message: "product.update.internal_id.not_blank")]
        public ?string $internalId = null,

        public ?string $description = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.update.quantity.not_blank"),
                new Assert\GreaterThan(value: 0, message: "product.update.quantity.uuid"),
            ]),
            new Assert\IsNull()
        ])]
        public ?int $quantity = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.update.category_id.not_blank"),
                new Assert\Uuid(message: "product.update.category_id.uuid"),
                new CategoryExists(message: "product.update.category_id.exists")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $categoryId = null,

        #[Assert\All([
            new Assert\Sequentially([
                new Assert\Uuid(message: "product.update.image_id.uuid"),
                new ImageExists(message: "product.update.image_id.exists")
            ])
        ])]
        public ?array $imageIds = [],

        #[Assert\NotNull(message: "product.update.deposit.money.not_null")]
        #[Assert\Valid]
        public ?MoneyInput $deposit = null,

        #[Assert\NotNull(message: "product.update.tiers.not_null")]
        #[Assert\Count(min: 1, minMessage: 'product.update.tiers.at_least_one')]
        #[Assert\Valid]
        public ?array $tiers = null,
    ) {
    }
}
