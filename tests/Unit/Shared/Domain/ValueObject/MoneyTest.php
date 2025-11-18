<?php declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Money;
use App\Shared\Exception\InvalidMoneyException;
use App\Tests\Unit\UnitTestCase;

class MoneyTest extends UnitTestCase
{
    public function testCreateValidMoney(): void
    {
        $money = new Money(100, 'EUR');

        self::assertSame(100, $money->getAmount());
        self::assertSame('EUR', $money->getCurrency(), 'Currency should be uppercased.');
    }

    public function testNegativeAmountThrowsException(): void
    {
        $this->expectException(InvalidMoneyException::class);

        new Money(-1, 'EUR');
    }

    public function testTooShortCurrencyCodeThrowsException(): void
    {
        $this->expectException(InvalidMoneyException::class);

        new Money(100, 'EU');
    }

    public function testTooLongCurrencyCodeThrowsException(): void
    {
        $this->expectException(InvalidMoneyException::class);

        new Money(100, 'EURO');
    }

    public function testNonAlphabeticCurrencyCodeThrowsException(): void
    {
        $this->expectException(InvalidMoneyException::class);

        new Money(100, 'E1R1');
    }

    public function testCurrencyNotAllowed(): void
    {
        $this->expectException(InvalidMoneyException::class);

        new Money(100, 'USD');
    }
}
