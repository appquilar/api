<?php declare(strict_types=1);

namespace App\Tests\Unit\Rent\Application\Command\UpdateRentStatus;

use App\Rent\Application\Command\UpdateRentStatus\UpdateRentStatusCommand;
use App\Rent\Application\Command\UpdateRentStatus\UpdateRentStatusCommandHandler;
use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentStatus;
use App\Rent\Domain\Service\RentAuthorisationServiceInterface;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class UpdateRentStatusCommandHandlerTest extends UnitTestCase
{
    private RentRepositoryInterface|MockObject $rentRepositoryMock;
    private RentAuthorisationServiceInterface|MockObject $rentAuthorisationServiceMock;
    private UpdateRentStatusCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rentRepositoryMock           = $this->createMock(RentRepositoryInterface::class);
        $this->rentAuthorisationServiceMock = $this->createMock(RentAuthorisationServiceInterface::class);

        $this->handler = new UpdateRentStatusCommandHandler(
            $this->rentRepositoryMock,
            $this->rentAuthorisationServiceMock,
        );
    }

    public function test_it_throws_exception_when_rent_not_found(): void
    {
        $rentId = Uuid::v4();
        $status = RentStatus::draft();

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
            ->method('canChangeStatus');

        $command = new UpdateRentStatusCommand(
            $rentId,
            $status
        );

        $this->expectException(EntityNotFoundException::class);

        ($this->handler)($command);
    }

    public function test_it_changes_status_when_authorised(): void
    {
        $rentId = Uuid::v4();
        $status = RentStatus::draft(); // o el que toque en tu flujo real

        $rentMock = $this->createMock(Rent::class);

        $this->rentRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($rentId)
            ->willReturn($rentMock);

        $this->rentAuthorisationServiceMock
            ->expects($this->once())
            ->method('canChangeStatus')
            ->with($rentMock, $status);

        $rentMock
            ->expects($this->once())
            ->method('transitionTo')
            ->with($status);

        $this->rentRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($rentMock);

        $command = new UpdateRentStatusCommand(
            $rentId,
            $status
        );

        ($this->handler)($command);
    }
}
