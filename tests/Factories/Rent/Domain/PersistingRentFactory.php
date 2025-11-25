<?php declare(strict_types=1);

namespace App\Tests\Factories\Rent\Domain;

use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentOwnerType;
use App\Rent\Domain\Enum\RentStatus;
use App\Shared\Domain\ValueObject\Money;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

class PersistingRentFactory extends PersistentObjectFactory
{

    protected function defaults(): array|callable
    {
        $startDate = self::faker()->dateTimeBetween('-1 month', 'now');

        return [
            'rentId' => Uuid::v4(),
            'productId' => Uuid::v4(),
            'ownerId' => Uuid::v4(),
            'ownerType' => RentOwnerType::USER,
            'renterId' => Uuid::v4(),
            'startDate' => $startDate,
            'endDate' => self::faker()->dateTimeBetween($startDate, '+1 month'),
            'deposit' => $this->randomMoney(),
            'price' => $this->randomMoney(),
            'depositReturned' => $this->randomMoney(),
            'status' => RentStatus::draft()
        ];
    }

    protected function initialize(): static
    {
        return $this->with($this->defaults())
            ;
    }

    public static function class(): string
    {
        return Rent::class;
    }

    private function randomMoney(): Money
    {
        return new Money(
            self::faker()->randomNumber(2) * 100,
            'EUR'
        );
    }
}
