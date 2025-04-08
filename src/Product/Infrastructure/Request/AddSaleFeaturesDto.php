<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Request;

use App\Product\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class AddSaleFeaturesDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.sale.product_id.not_blank"),
            new Assert\Uuid(message: "product.sale.product_id.uuid"),
        ])]
        public ?Uuid $productId = null,

        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.sale.price_amount.not_blank"),
            new Assert\Positive(message: "product.sale.price_amount.positive"),
        ])]
        public ?float $priceAmount = null,

        #[Assert\NotBlank(message: "product.sale.price_currency.not_blank")]
        public string $priceCurrency = 'EUR',

        public ?string $condition = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.sale.year_of_purchase.not_blank"),
                new Assert\Range(minMessage: "product.sale.year_of_purchase.min", maxMessage: "product.sale.year_of_purchase.max", min: 1900, max: 2100),
            ]),
            new Assert\IsNull()
        ])]
        public ?int $yearOfPurchase = null,

        public bool $negotiable = false,

        public ?string $additionalInformation = null
    ) {
    }

    public function getPrice(): Money
    {
        return new Money($this->priceAmount, $this->priceCurrency);
    }
}
