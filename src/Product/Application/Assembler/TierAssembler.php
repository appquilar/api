<?php declare(strict_types=1);

namespace App\Product\Application\Assembler;

use App\Product\Domain\Exception\InvalidPriceConstructionException;
use App\Product\Domain\ValueObject\Tier;
use App\Product\Domain\ValueObject\TierCollection;
use App\Product\Infrastructure\Request\Input\TierInput;
use App\Shared\Domain\ValueObject\Money;

final class TierAssembler
{
    /**
     * @throws InvalidPriceConstructionException
     */
    public function createTierFromInput(TierInput $tierInput): Tier
    {
        return new Tier(
            pricePerDay: new Money(
                amount: $tierInput->pricePerDay->amount,
                currency: $tierInput->pricePerDay->currency,
            ),
            daysFrom: $tierInput->daysFrom,
            daysTo: $tierInput->daysTo
        );
    }

    /**
     * @param TierInput[] $tiers
     * @return TierCollection
     * @throws InvalidPriceConstructionException
     */
    public function createTierCollectionFromArrayOfTierInputs(array $tiers): TierCollection
    {
        return new TierCollection(
            array_map(
                fn (TierInput $tier) => $this->createTierFromInput($tier),
                $tiers
            )
        );
    }
}