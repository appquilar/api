<?php

declare(strict_types=1);

namespace App\Product\Application\Command\AddRentalFeatures;

use App\Product\Application\Command\ProductCommand;
use App\Product\Domain\ValueObject\Money;
use Symfony\Component\Uid\Uuid;

class AddRentalFeaturesCommand extends ProductCommand
{
    public function __construct(
        Uuid $productId,
        private ?Money $dailyPrice = null,
        private ?Money $hourlyPrice = null,
        private ?Money $weeklyPrice = null,
        private ?Money $monthlyPrice = null,
        private ?Money $deposit = null
    ) {
        parent::__construct($productId);
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
}
