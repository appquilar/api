<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use App\Product\Domain\ValueObject\Money;
use App\Shared\Domain\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "rental_products")]
class RentalProduct extends Entity
{
    #[ORM\OneToOne(inversedBy: "rentalProduct")]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id", nullable: false)]
    private Product $product;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'daily_')]
    private ?Money $dailyPrice = null;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'hourly_')]
    private ?Money $hourlyPrice = null;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'weekly_')]
    private ?Money $weeklyPrice = null;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'monthly_')]
    private ?Money $monthlyPrice = null;

    #[ORM\Embedded(class: Money::class, columnPrefix: 'deposit_')]
    private ?Money $deposit = null;

    #[ORM\Column(type: "boolean")]
    private bool $alwaysAvailable = false;

    #[ORM\Column(type: "json")]
    private array $availabilityPeriods = [];

    #[ORM\Column(type: "boolean")]
    private bool $includesWeekends = false;

    public function __construct(
        Product $product,
        ?Money $dailyPrice,
        ?Money $hourlyPrice = null,
        ?Money $weeklyPrice = null,
        ?Money $monthlyPrice = null,
        ?Money $deposit = null,
        bool $alwaysAvailable = false,
        array $availabilityPeriods = [],
        bool $includesWeekends = true
    ) {
        parent::__construct(Uuid::v4());

        $this->product = $product;
        $this->dailyPrice = $dailyPrice;
        $this->hourlyPrice = $hourlyPrice;
        $this->weeklyPrice = $weeklyPrice;
        $this->monthlyPrice = $monthlyPrice;
        $this->deposit = $deposit;
        $this->alwaysAvailable = $alwaysAvailable;
        $this->availabilityPeriods = $availabilityPeriods;
        $this->includesWeekends = $includesWeekends;

        $product->addRentalProduct($this);
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getDailyPrice(): ?Money
    {
        return $this->dailyPrice;
    }

    public function getHourlyPrice(): ?Money
    {
        return $this->hourlyPrice;
    }

    public function getWeeklyPrice(): ?Money
    {
        return $this->weeklyPrice;
    }

    public function getMonthlyPrice(): ?Money
    {
        return $this->monthlyPrice;
    }

    public function getDeposit(): ?Money
    {
        return $this->deposit;
    }

    public function isAlwaysAvailable(): bool
    {
        return $this->alwaysAvailable;
    }

    public function getAvailabilityPeriods(): array
    {
        return $this->availabilityPeriods;
    }

    public function includesWeekends(): bool
    {
        return $this->includesWeekends;
    }

    public function update(
        ?Money $dailyPrice = null,
        ?Money $hourlyPrice = null,
        ?Money $weeklyPrice = null,
        ?Money $monthlyPrice = null,
        ?Money $deposit = null,
        bool $alwaysAvailable = false,
        array $availabilityPeriods = [],
        bool $includesWeekends = true
    ): void {
        $this->dailyPrice = $dailyPrice;
        $this->hourlyPrice = $hourlyPrice;
        $this->weeklyPrice = $weeklyPrice;
        $this->monthlyPrice = $monthlyPrice;
        $this->deposit = $deposit;
        $this->alwaysAvailable = $alwaysAvailable;
        $this->availabilityPeriods = $availabilityPeriods;
        $this->includesWeekends = $includesWeekends;
    }
}
