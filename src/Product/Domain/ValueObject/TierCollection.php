<?php declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

use App\Product\Domain\Exception\InvalidPriceConstructionException;

final class TierCollection
{
    /** @param Tier[] $tiers
     * @throws InvalidPriceConstructionException
     */
    public function __construct(private array $tiers)
    {
        if ($tiers === []) {
            throw new \InvalidArgumentException('Tier collection cannot be empty');
        }

        $tiers = $this->orderTierByDayFrom($tiers);

        $this->validateTiersDoNotOverlap($tiers);

        $this->tiers = $tiers;
    }

    /**
     * @param Tier[] $tiers
     * @throws InvalidPriceConstructionException
     */
    private function validateTiersDoNotOverlap(array $tiers): void
    {
        $prevTo = 0;
        foreach ($tiers as $tier) {
            if ($tier->getDaysFrom() <= $prevTo) {
                throw new InvalidPriceConstructionException('tiers.tier.cannot_overlap');
            }
            $prevTo = $tier->getDaysTo() ?? PHP_INT_MAX;
        }
    }

    /**
     * @param Tier[] $tiers
     * @return Tier[]
     */
    public function orderTierByDayFrom(array $tiers): array
    {
        usort($tiers, fn(Tier $a, Tier $b) => $a->getDaysFrom() <=> $b->getDaysFrom());

        return $tiers;
    }

    public function findForDays(int $days): ?Tier
    {
        return array_find(
            $this->tiers,
            fn(Tier $tier) => $tier->matches($days)
        );
    }

    public function toArray(): array
    {
        return array_map(fn(Tier $t) => $t->toArray(), $this->tiers);
    }

    /**
     * @param array $rows
     * @return self
     * @throws InvalidPriceConstructionException
     */
    public static function fromArray(array $rows): self
    {
        return new self(
            array_map(fn($row) => Tier::fromArray($row), $rows)
        );
    }
}