<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Domain\ValueObject;

use App\Product\Domain\Exception\InvalidPriceConstructionException;
use App\Product\Domain\ValueObject\Tier;
use App\Shared\Domain\ValueObject\Money;
use App\Tests\Unit\UnitTestCase;

class TierTest extends UnitTestCase
{
    public function test_construct_valid_tier(): void
    {
        $money = new Money(1000, 'EUR');

        $tier = new Tier($money, 1, 3);

        $this->assertSame($money, $tier->getPricePerDay());
        $this->assertSame(1, $tier->getDaysFrom());
        $this->assertSame(3, $tier->getDaysTo());
    }

    public function test_construct_open_ended_tier(): void
    {
        $money = new Money(2000, 'EUR');

        $tier = new Tier($money, 5, null);

        $this->assertSame(5, $tier->getDaysFrom());
        $this->assertNull($tier->getDaysTo());
    }

    public function test_construct_with_negative_days_from_throws_exception(): void
    {
        $money = new Money(1000, 'EUR');

        $this->expectException(InvalidPriceConstructionException::class);
        $this->expectExceptionMessage('tier.days_from_must_be_positive');

        new Tier($money, 0, 3);
    }

    public function test_construct_with_days_to_smaller_than_days_from_throws_exception(): void
    {
        $money = new Money(1000, 'EUR');

        $this->expectException(InvalidPriceConstructionException::class);
        $this->expectExceptionMessage('tier.days_to_must_be_bigger_than_days_from');

        new Tier($money, 5, 3);
    }

    public function test_matches_for_range_and_open_ended(): void
    {
        $money = new Money(1000, 'EUR');

        $rangeTier = new Tier($money, 1, 3);
        $openTier  = new Tier($money, 4, null);

        // range tier
        $this->assertTrue($rangeTier->matches(1));
        $this->assertTrue($rangeTier->matches(2));
        $this->assertTrue($rangeTier->matches(3));
        $this->assertFalse($rangeTier->matches(4));

        // open-ended tier
        $this->assertTrue($openTier->matches(4));
        $this->assertTrue($openTier->matches(1000));
        $this->assertFalse($openTier->matches(3));
    }

    public function test_to_array_and_from_array_are_consistent(): void
    {
        $money = new Money(1500, 'EUR');

        $tier = new Tier($money, 2, 5);

        $array = $tier->toArray();

        $this->assertSame(
            [
                'price_per_day' => $money->toArray(),
                'days_from'     => 2,
                'days_to'       => 5,
            ],
            $array
        );

        $tierFromArray = Tier::fromArray($array);

        $this->assertSame($tier->getDaysFrom(), $tierFromArray->getDaysFrom());
        $this->assertSame($tier->getDaysTo(), $tierFromArray->getDaysTo());
        $this->assertSame(
            $tier->getPricePerDay()->toArray(),
            $tierFromArray->getPricePerDay()->toArray()
        );
    }

    public function test_from_array_open_ended_tier(): void
    {
        $money = new Money(2000, 'EUR');

        $data = [
            'price_per_day' => $money->toArray(),
            'days_from'     => 10,
            // 'days_to' intentionally omitted
        ];

        $tier = Tier::fromArray($data);

        $this->assertSame(10, $tier->getDaysFrom());
        $this->assertNull($tier->getDaysTo());
    }
}
