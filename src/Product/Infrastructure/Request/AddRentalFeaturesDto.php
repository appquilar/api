<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Request;

use App\Product\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class AddRentalFeaturesDto implements RequestDtoInterface
{
    /**
     * @param array<string, array<string, mixed>>|null $availabilityPeriods
     */
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.rental.product_id.not_blank"),
            new Assert\Uuid(message: "product.rental.product_id.uuid"),
        ])]
        public ?Uuid $productId = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.rental.daily_price.not_blank"),
                new Assert\Positive(message: "product.rental.daily_price.positive"),
            ]),
            new Assert\IsNull()
        ])]
        public ?float $dailyPriceAmount = null,

        public string $dailyPriceCurrency = 'EUR',

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.rental.hourly_price.not_blank"),
                new Assert\Positive(message: "product.rental.hourly_price.positive"),
            ]),
            new Assert\IsNull()
        ])]
        public ?float $hourlyPriceAmount = null,

        public ?string $hourlyPriceCurrency = 'EUR',

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.rental.weekly_price.not_blank"),
                new Assert\Positive(message: "product.rental.weekly_price.positive"),
            ]),
            new Assert\IsNull()
        ])]
        public ?float $weeklyPriceAmount = null,

        public ?string $weeklyPriceCurrency = 'EUR',

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.rental.monthly_price.not_blank"),
                new Assert\Positive(message: "product.rental.monthly_price.positive"),
            ]),
            new Assert\IsNull()
        ])]
        public ?float $monthlyPriceAmount = null,

        public ?string $monthlyPriceCurrency = 'EUR',

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.rental.deposit.not_blank"),
                new Assert\PositiveOrZero(message: "product.rental.deposit.positive_or_zero"),
            ]),
            new Assert\IsNull()
        ])]
        public ?float $depositAmount = null,

        public ?string $depositCurrency = 'EUR',

        public bool $alwaysAvailable = false,

        public array $availabilityPeriods = [],

        public bool $includesWeekends = true
    ) {
    }

    public function getDailyPrice(): ?Money
    {
        if ($this->dailyPriceAmount === null) {
            return null;
        }

        return new Money($this->dailyPriceAmount, $this->dailyPriceCurrency);
    }

    public function getHourlyPrice(): ?Money
    {
        if ($this->hourlyPriceAmount === null) {
            return null;
        }

        return new Money($this->hourlyPriceAmount, $this->hourlyPriceCurrency ?? 'EUR');
    }

    public function getWeeklyPrice(): ?Money
    {
        if ($this->weeklyPriceAmount === null) {
            return null;
        }

        return new Money($this->weeklyPriceAmount, $this->weeklyPriceCurrency ?? 'EUR');
    }

    public function getMonthlyPrice(): ?Money
    {
        if ($this->monthlyPriceAmount === null) {
            return null;
        }

        return new Money($this->monthlyPriceAmount, $this->monthlyPriceCurrency ?? 'EUR');
    }

    public function getDeposit(): ?Money
    {
        if ($this->depositAmount === null) {
            return null;
        }

        return new Money($this->depositAmount, $this->depositCurrency ?? 'EUR');
    }
}
