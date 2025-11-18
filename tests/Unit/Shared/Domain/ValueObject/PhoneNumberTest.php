<?php declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\ValueObject;

use App\Shared\Domain\ValueObject\PhoneNumber;
use App\Tests\Unit\UnitTestCase;

class PhoneNumberTest extends UnitTestCase
{
    public function testCreateValidPhoneNumber(): void
    {
        $phone = new PhoneNumber('ES', '34', '600111222');

        self::assertSame('ES', $phone->toArray()['country_code']);
        self::assertSame('34', $phone->toArray()['prefix']);
        self::assertSame('600111222', $phone->toArray()['number']);
    }
}
