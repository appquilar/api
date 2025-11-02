<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use App\Product\Domain\ValueObject\Money;
use App\Shared\Domain\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "rental_products")]
class RentalProduct extends Entity
{
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $productId;

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

    public function __construct(
        Uuid $productId,
        ?Money $dailyPrice,
        ?Money $hourlyPrice = null,
        ?Money $weeklyPrice = null,
        ?Money $monthlyPrice = null,
        ?Money $deposit = null
    ) {
        parent::__construct(Uuid::v4());

        $this->productId = $productId;
        $this->dailyPrice = $dailyPrice;
        $this->hourlyPrice = $hourlyPrice;
        $this->weeklyPrice = $weeklyPrice;
        $this->monthlyPrice = $monthlyPrice;
        $this->deposit = $deposit;
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
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

    public function update(
        ?Money $dailyPrice = null,
        ?Money $hourlyPrice = null,
        ?Money $weeklyPrice = null,
        ?Money $monthlyPrice = null,
        ?Money $deposit = null
    ): void {
        $this->dailyPrice = $dailyPrice;
        $this->hourlyPrice = $hourlyPrice;
        $this->weeklyPrice = $weeklyPrice;
        $this->monthlyPrice = $monthlyPrice;
        $this->deposit = $deposit;
    }
}
