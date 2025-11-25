<?php declare(strict_types=1);

namespace App\Tests\Unit\Rent\Application\Command\UpdateRent;

use App\Rent\Application\Command\UpdateRent\UpdateRentCommand;
use App\Rent\Application\Command\UpdateRent\UpdateRentCommandHandler;
use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Service\RentAuthorisationServiceInterface;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Domain\ValueObject\Money;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class UpdateRentCommandHandlerTest extends UnitTestCase
{
    private RentRepositoryInterface|MockObject $rentRepositoryMock;
    private RentAuthorisationServiceInterface|MockObject $rentAuthorisationServiceMock;
    private UpdateRentCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rentRepositoryMock           = $this->createMock(RentRepositoryInterface::class);
        $this->rentAuthorisationServiceMock = $this->createMock(RentAuthorisationServiceInterface::class);

        $this->handler = new UpdateRentCommandHandler(
            $this->rentRepositoryMock,
            $this->rentAuthorisationServiceMock,
        );
    }

    public function test_it_throws_exception_when_rent_not_found(): void
    {
        $rentId = Uuid::v4();

        $this->rentRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($rentId)
            ->willReturn(null);

        $this->rentRepositoryMock
            ->expects($this->never())
            ->method('save');

        $this->rentAuthorisationServiceMock
            ->expects($this->never())
            ->method('canEdit');

        $command = new UpdateRentCommand(
            $rentId,
            new \DateTime('2025-01-02 10:00:00'),
            new \DateTime('2025-01-12 10:00:00'),
            new Money(20_000, 'EUR'),
            new Money(60_000, 'EUR'),
            new Money(5_000, 'EUR')
        );

        $this->expectException(EntityNotFoundException::class);

        ($this->handler)($command);
    }

    public function test_it_updates_dates_and_money_when_values_are_provided(): void
    {
        $rentId = Uuid::v4();

        $rentMock = $this->createMock(Rent::class);

        $startDate = new \DateTime('2025-01-02 10:00:00 Europe/Berlin');
        $endDate   = new \DateTime('2025-01-12 10:00:00 Europe/Berlin');
        $deposit   = new Money(20_000, 'EUR');
        $price     = new Money(60_000, 'EUR');
        $returned  = new Money(5_000, 'EUR');

        $command = new UpdateRentCommand(
            $rentId,
            $startDate,
            $endDate,
            $deposit,
            $price,
            $returned
        );

        $this->rentRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($rentId)
            ->willReturn($rentMock);

        $this->rentAuthorisationServiceMock
            ->expects($this->once())
            ->method('canEdit')
            ->with($rentMock);

        $this->rentAuthorisationServiceMock
            ->expects($this->exactly(3))
            ->method('canChangePrice')
            ->with($rentMock);

        $rentMock
            ->expects($this->once())
            ->method('setStartDate')
            ->with($startDate);

        $rentMock
            ->expects($this->once())
            ->method('setEndDate')
            ->with($endDate);

        $rentMock
            ->expects($this->once())
            ->method('setDeposit')
            ->with($deposit);

        $rentMock
            ->expects($this->once())
            ->method('setPrice')
            ->with($price);

        $rentMock
            ->expects($this->once())
            ->method('setDepositReturned')
            ->with($returned);

        $this->rentRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($rentMock);

        ($this->handler)($command);
    }
}
