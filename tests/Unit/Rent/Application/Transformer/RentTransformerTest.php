<?php declare(strict_types=1);

namespace App\Tests\Unit\Rent\Application\Transformer;

use App\Rent\Application\Transformer\RentTransformer;
use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentOwnerType;
use App\Rent\Domain\Enum\RentStatus;
use App\Shared\Domain\ValueObject\Money;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Uid\Uuid;

class RentTransformerTest extends UnitTestCase
{
    private RentTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer = new RentTransformer();
    }

    private function createRent(
        ?Money $depositReturned
    ): Rent {
        $rentId    = Uuid::v4();
        $productId = Uuid::v4();
        $ownerId   = Uuid::v4();
        $renterId  = Uuid::v4();

        $startDate = new \DateTime('2025-01-01 10:00:00 Europe/Madrid');
        $endDate   = new \DateTime('2025-01-10 10:00:00 Europe/Madrid');

        $deposit = new Money(10000, 'EUR');
        $price   = new Money(50000, 'EUR');

        return new Rent(
            rentId: $rentId,
            productId: $productId,
            ownerId: $ownerId,
            ownerType: RentOwnerType::COMPANY,
            renterId: $renterId,
            startDate: $startDate,
            endDate: $endDate,
            deposit: $deposit,
            price: $price,
            depositReturned: $depositReturned,
            status: RentStatus::PENDING
        );
    }

    public function test_transform_with_null_deposit_returned(): void
    {
        $rent = $this->createRent(depositReturned: null);

        $result = $this->transformer->transform($rent);

        $this->assertSame($rent->getId()->toString(), $result['rent_id']);
        $this->assertSame($rent->getProductId()->toString(), $result['product_id']);
        $this->assertSame($rent->getOwnerId()->toString(), $result['owner_id']);
        $this->assertSame($rent->getOwnerType()->value, $result['owner_type']);
        $this->assertSame($rent->getRenterId()->toString(), $result['renter_id']);

        $this->assertSame(
            $rent->getStartDate()->format('Y-m-d H:i:s e'),
            $result['start_date']
        );
        $this->assertSame(
            $rent->getEndDate()->format('Y-m-d H:i:s e'),
            $result['end_date']
        );

        $this->assertSame($rent->getDeposit()->getAmount(), $result['deposit']['amount']);
        $this->assertSame($rent->getDeposit()->getCurrency(), $result['deposit']['currency']);

        $this->assertSame($rent->getPrice()->getAmount(), $result['price']['amount']);
        $this->assertSame($rent->getPrice()->getCurrency(), $result['price']['currency']);

        $this->assertArrayHasKey('deposit_returned', $result);
        $this->assertNull($result['deposit_returned']);

        $this->assertSame($rent->getStatus()->value, $result['status']);
    }

    public function test_transform_with_non_null_deposit_returned(): void
    {
        $depositReturned = new Money(5000, 'EUR');
        $rent = $this->createRent(depositReturned: $depositReturned);

        $result = $this->transformer->transform($rent);

        $this->assertNotNull($result['deposit_returned']);
        $this->assertSame($depositReturned->getAmount(), $result['deposit_returned']['amount']);
        $this->assertSame($depositReturned->getCurrency(), $result['deposit_returned']['currency']);
    }
}
