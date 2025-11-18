<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Request\Input;

use App\Shared\Infrastructure\Request\Input\MoneyInput;
use Symfony\Component\Validator\Constraints as Assert;

final class TierInput
{
    public function __construct(
        #[Assert\NotBlank(message: "product.tier.money.not_null")]
        #[Assert\Positive(message: "product.tier.money.not_null")]
        public ?int $daysFrom = null,

        // nullable → open-ended
        #[Assert\AtLeastOneOf([
            new Assert\IsNull(message: "product.tier.money.not_null"),
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.tier.money.not_null"),
                new Assert\Positive(message: "product.tier.money.not_null"),
            ])
        ])]
        public ?int $daysTo = null,

        #[Assert\NotNull(message: "product.tier.money.not_null")]
        #[Assert\Valid]
        public ?MoneyInput $pricePerDay = null,
    ) {}
}