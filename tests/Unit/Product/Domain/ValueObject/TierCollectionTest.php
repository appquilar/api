<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Domain\ValueObject;

use App\Product\Domain\Exception\InvalidPriceConstructionException;
use App\Product\Domain\ValueObject\Tier;
use App\Product\Domain\ValueObject\TierCollection;
use App\Shared\Domain\ValueObject\Money;
use App\Tests\Unit\UnitTestCase;

class TierCollectionTest extends UnitTestCase
{
    public function test_construct_with_empty_array_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Tier collection cannot be empty');

        new TierCollection([]);
    }

    public function test_construct_with_overlapping_tiers_throws_exception(): void
    {
        $money = new Money(1000, 'EUR');

        $tier1 = new Tier($money, 1, 3);
        $tier2 = new Tier($money, 3, 5); // 3 <= prevTo (3) → solapamiento

        $this->expectException(InvalidPriceConstructionException::class);
        $this->expectExceptionMessage('tiers.tier.cannot_overlap');

        new TierCollection([$tier1, $tier2]);
    }

    public function test_construct_with_non_overlapping_tiers_orders_them_by_days_from(): void
    {
        $money = new Money(1000, 'EUR');

        $tier1 = new Tier($money, 5, 7);
        $tier2 = new Tier($money, 1, 2);
        $tier3 = new Tier($money, 10, null);

        $collection = new TierCollection([$tier1, $tier3, $tier2]);

        // No excepción = colección válida
        // orderTierByDayFrom se usa en el constructor, comprobamos indirectly con findForDays
        $this->assertSame($tier2, $collection->findForDays(1));
        $this->assertSame($tier1, $collection->findForDays(6));
        $this->assertSame($tier3, $collection->findForDays(50));
    }

    public function test_find_for_days_returns_null_when_no_tier_matches(): void
    {
        $money = new Money(1000, 'EUR');

        $tier1 = new Tier($money, 1, 3);
        $tier2 = new Tier($money, 4, 7);

        $collection = new TierCollection([$tier1, $tier2]);

        $this->assertNull($collection->findForDays(0));
        $this->assertNull($collection->findForDays(8));
    }

    public function test_to_array_maps_tiers_to_array(): void
    {
        $money = new Money(1000, 'EUR');

        $tier1 = new Tier($money, 1, 3);
        $tier2 = new Tier($money, 4, 7);

        $collection = new TierCollection([$tier2, $tier1]); // orden de entrada da igual

        $array = $collection->toArray();

        $this->assertCount(2, $array);
        // No comprobamos el orden exacto del array de salida, solo la estructura básica
        $this->assertSame($tier1->toArray(), $array[0]);
        $this->assertSame($tier2->toArray(), $array[1]);
    }

    public function test_from_array_builds_collection_and_validates_tiers(): void
    {
        $money1 = new Money(1000, 'EUR');
        $money2 = new Money(2000, 'EUR');

        $rows = [
            [
                'price_per_day' => $money1->toArray(),
                'days_from'     => 1,
                'days_to'       => 3,
            ],
            [
                'price_per_day' => $money2->toArray(),
                'days_from'     => 4,
                'days_to'       => 7,
            ],
        ];

        $collection = TierCollection::fromArray($rows);

        $this->assertNotNull($collection->findForDays(2));
        $this->assertNotNull($collection->findForDays(5));
    }

    public function test_from_array_with_overlapping_rows_throws_exception(): void
    {
        $money1 = new Money(1000, 'EUR');
        $money2 = new Money(2000, 'EUR');

        $rows = [
            [
                'price_per_day' => $money1->toArray(),
                'days_from'     => 1,
                'days_to'       => 3,
            ],
            [
                'price_per_day' => $money2->toArray(),
                'days_from'     => 3,
                'days_to'       => 7,
            ],
        ];

        $this->expectException(InvalidPriceConstructionException::class);
        $this->expectExceptionMessage('tiers.tier.cannot_overlap');

        TierCollection::fromArray($rows);
    }
}
