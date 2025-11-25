<?php declare(strict_types=1);

namespace App\Tests\Unit\Rent\Domain\Entity;

use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentOwnerType;
use App\Rent\Domain\Enum\RentStatus;
use App\Rent\Domain\Exception\WrongRentStatusTransition;
use App\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class RentTest extends TestCase
{
    private function createRent(
        RentStatus $status = RentStatus::PENDING,
        ?Money $depositReturned = null
    ): Rent {
        return new Rent(
            rentId: Uuid::v4(),
            productId: Uuid::v4(),
            ownerId: Uuid::v4(),
            ownerType: RentOwnerType::USER,
            renterId: Uuid::v4(),
            startDate: new \DateTime('2025-01-01 10:00:00'),
            endDate: new \DateTime('2025-01-10 10:00:00'),
            deposit: new Money(10000, 'EUR'),
            price: new Money(5000, 'EUR'),
            depositReturned: $depositReturned,
            status: $status
        );
    }

    public function test_constructor_sets_all_properties(): void
    {
        $id = Uuid::v4();
        $productId = Uuid::v4();
        $ownerId = Uuid::v4();
        $renterId = Uuid::v4();

        $deposit = new Money(10000, 'EUR');
        $price = new Money(5000, 'EUR');

        $start = new \DateTime('2025-01-01 09:00:00');
        $end   = new \DateTime('2025-01-05 18:00:00');

        $rent = new Rent(
            rentId: $id,
            productId: $productId,
            ownerId: $ownerId,
            ownerType: RentOwnerType::USER,
            renterId: $renterId,
            startDate: $start,
            endDate: $end,
            deposit: $deposit,
            price: $price,
            depositReturned: null,
            status: RentStatus::PENDING
        );

        $this->assertSame($id, $rent->getId());
        $this->assertSame($productId, $rent->getProductId());
        $this->assertSame($ownerId, $rent->getOwnerId());
        $this->assertSame(RentOwnerType::USER, $rent->getOwnerType());
        $this->assertSame($renterId, $rent->getRenterId());
        $this->assertSame($start, $rent->getStartDate());
        $this->assertSame($end, $rent->getEndDate());
        $this->assertSame($deposit, $rent->getDeposit());
        $this->assertSame($price, $rent->getPrice());
        $this->assertNull($rent->getDepositReturned());
        $this->assertSame(RentStatus::PENDING, $rent->getStatus());
    }

    public function test_setters_work_correctly(): void
    {
        $rent = $this->createRent();

        $newProductId = Uuid::v4();
        $newOwnerId = Uuid::v4();
        $newRenterId = Uuid::v4();
        $newDeposit = new Money(20000, 'EUR');
        $newPrice = new Money(7000, 'EUR');
        $newDepositReturned = new Money(15000, 'EUR');
        $newStart = new \DateTime('2025-02-01 10:00:00');
        $newEnd = new \DateTime('2025-02-10 10:00:00');

        $rent->setProductId($newProductId);
        $rent->setOwnerId($newOwnerId);
        $rent->setOwnerType(RentOwnerType::COMPANY);
        $rent->setRenterId($newRenterId);
        $rent->setDeposit($newDeposit);
        $rent->setPrice($newPrice);
        $rent->setDepositReturned($newDepositReturned);
        $rent->setStartDate($newStart);
        $rent->setEndDate($newEnd);
        $rent->setStatus(RentStatus::CONFIRMED);

        $this->assertSame($newProductId, $rent->getProductId());
        $this->assertSame($newOwnerId, $rent->getOwnerId());
        $this->assertSame(RentOwnerType::COMPANY, $rent->getOwnerType());
        $this->assertSame($newRenterId, $rent->getRenterId());
        $this->assertSame($newDeposit, $rent->getDeposit());
        $this->assertSame($newPrice, $rent->getPrice());
        $this->assertSame($newDepositReturned, $rent->getDepositReturned());
        $this->assertSame($newStart, $rent->getStartDate());
        $this->assertSame($newEnd, $rent->getEndDate());
        $this->assertSame(RentStatus::CONFIRMED, $rent->getStatus());
    }

    public function test_transition_to_valid_status(): void
    {
        $rent = $this->createRent(status: RentStatus::PENDING);

        $rent->transitionTo(RentStatus::CONFIRMED);

        $this->assertSame(RentStatus::CONFIRMED, $rent->getStatus());
    }

    public function test_transition_to_invalid_status_throws_exception(): void
    {
        $rent = $this->createRent(status: RentStatus::COMPLETED);

        $this->expectException(WrongRentStatusTransition::class);
        $this->expectExceptionMessage('rent.transition.from.to.completed.pending');

        $rent->transitionTo(RentStatus::PENDING);
    }

    public function test_deposit_returned_can_be_null_or_money(): void
    {
        $rent1 = $this->createRent(depositReturned: null);
        $rent2 = $this->createRent(depositReturned: new Money(5000, 'EUR'));

        $this->assertNull($rent1->getDepositReturned());
        $this->assertInstanceOf(Money::class, $rent2->getDepositReturned());
        $this->assertEquals(5000, $rent2->getDepositReturned()->getAmount());
    }
}
