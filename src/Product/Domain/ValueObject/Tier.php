<?php declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

use App\Product\Domain\Exception\InvalidPriceConstructionException;
use App\Shared\Domain\ValueObject\Money;

final class Tier
{
    /**
     * @throws InvalidPriceConstructionException
     */
    public function __construct(
        private Money $pricePerDay,
        private int   $daysFrom,
        private ?int  $daysTo = null
    ) {
        $this->validate($this->daysFrom, $this->daysTo);
    }

    /**
     * @throws InvalidPriceConstructionException
     */
    private function validate(
        int $daysFrom,
        ?int $daysTo = null
    ): void {
        $this->validateDaysFromIsPositive($daysFrom);
        $this->validateDaysToIsBiggerThanDaysFrom($daysFrom, $daysTo);
    }

    private function validateDaysFromIsPositive(int $daysFrom): void
    {
        if ($daysFrom < 1) {
            throw new InvalidPriceConstructionException('tier.days_from_must_be_positive');
        }
    }

    private function validateDaysToIsBiggerThanDaysFrom(
        int $daysFrom,
        ?int $daysTo = null
    ): void {
        if ($daysTo !== null && $daysFrom > $daysTo) {
            throw new InvalidPriceConstructionException('tier.days_to_must_be_bigger_than_days_from');
        }
    }

    public function getPricePerDay(): Money
    {
        return $this->pricePerDay;
    }

    public function getDaysFrom(): int
    {
        return $this->daysFrom;
    }

    public function getDaysTo(): ?int
    {
        return $this->daysTo;
    }

    public function matches(int $days): bool
    {
        $max = $this->daysTo ?? PHP_INT_MAX;
        
        return $days >= $this->daysFrom && $days <= $max;
    }

    /**
     * @throws InvalidPriceConstructionException
     */
    public static function fromArray(array $data): self
    {
        return new self(
            Money::fromArray($data['price_per_day']),
            $data['days_from'],
            array_key_exists('days_to', $data) ? $data['days_to'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'price_per_day' => $this->getPricePerDay()->toArray(),
            'days_from' => $this->getDaysFrom(),
            'days_to' => $this->getDaysTo(),
        ];
    }
}
