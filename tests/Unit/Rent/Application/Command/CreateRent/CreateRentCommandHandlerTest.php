<?php declare(strict_types=1);

namespace App\Tests\Unit\Rent\Application\Command\CreateRent;

use App\Rent\Application\Command\CreateRent\CreateRentCommand;
use App\Rent\Application\Command\CreateRent\CreateRentCommandHandler;
use App\Rent\Application\Dto\RentProductDto;
use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Application\Service\RentProductServiceInterface;
use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentOwnerType;
use App\Rent\Domain\Enum\RentStatus;
use App\Rent\Domain\Service\RentAuthorisationServiceInterface;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Domain\ValueObject\Money;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class CreateRentCommandHandlerTest extends UnitTestCase
{
    private RentRepositoryInterface|MockObject $rentRepositoryMock;
    private RentAuthorisationServiceInterface|MockObject $rentAuthorisationServiceMock;
    private RentProductServiceInterface|MockObject $rentProductServiceMock;
    private CreateRentCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rentRepositoryMock         = $this->createMock(RentRepositoryInterface::class);
        $this->rentAuthorisationServiceMock = $this->createMock(RentAuthorisationServiceInterface::class);
        $this->rentProductServiceMock     = $this->createMock(RentProductServiceInterface::class);

        $this->handler = new CreateRentCommandHandler(
            $this->rentRepositoryMock,
            $this->rentAuthorisationServiceMock,
            $this->rentProductServiceMock,
        );
    }

    public function test_rent_is_created_when_product_exists(): void
    {
        $rentId    = Uuid::v4();
        $productId = Uuid::v4();
        $ownerId   = Uuid::v4();
        $renterId  = Uuid::v4();

        $startDate = new \DateTime('2025-01-01 10:00:00 Europe/Madrid');
        $endDate   = new \DateTime('2025-01-10 10:00:00 Europe/Madrid');

        $deposit = new Money(10_000, 'EUR');
        $price   = new Money(50_000, 'EUR');

        $command = new CreateRentCommand(
            $rentId,
            $productId,
            $renterId,
            $startDate,
            $endDate,
            $deposit,
            $price
        );

        $productOwnerType = RentOwnerType::COMPANY;

        $rentProductDto = new RentProductDto(
            $productId,
            $ownerId,
            $productOwnerType
        );

        $this->rentProductServiceMock
            ->expects($this->once())
            ->method('getProductOwnershipByProductId')
            ->with($productId)
            ->willReturn($rentProductDto);

        $this->rentAuthorisationServiceMock
            ->expects($this->once())
            ->method('canCreate')
            ->with($this->callback(function (Rent $rent) use (
                $rentId,
                $productId,
                $ownerId,
                $productOwnerType,
                $renterId,
                $startDate,
                $endDate,
                $deposit,
                $price
            ): bool {
                return $rent->getId()->equals($rentId)
                    && $rent->getProductId()->equals($productId)
                    && $rent->getOwnerId()->equals($ownerId)
                    && $rent->getOwnerType() === $productOwnerType
                    && $rent->getRenterId()->equals($renterId)
                    && $rent->getStartDate() == $startDate
                    && $rent->getEndDate() == $endDate
                    && $rent->getDeposit()->getAmount() === $deposit->getAmount()
                    && $rent->getDeposit()->getCurrency() === $deposit->getCurrency()
                    && $rent->getPrice()->getAmount() === $price->getAmount()
                    && $rent->getPrice()->getCurrency() === $price->getCurrency()
                    && $rent->getDepositReturned()->getAmount() === 0
                    && $rent->getStatus() === RentStatus::draft();
            }));

        $this->rentRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Rent $rent) use (
                $rentId,
                $productId,
                $ownerId,
                $productOwnerType,
                $renterId,
                $startDate,
                $endDate,
                $deposit,
                $price
            ): bool {
                return $rent->getId()->equals($rentId)
                    && $rent->getProductId()->equals($productId)
                    && $rent->getOwnerId()->equals($ownerId)
                    && $rent->getOwnerType() === $productOwnerType
                    && $rent->getRenterId()->equals($renterId)
                    && $rent->getStartDate() == $startDate
                    && $rent->getEndDate() == $endDate
                    && $rent->getDeposit()->getAmount() === $deposit->getAmount()
                    && $rent->getDeposit()->getCurrency() === $deposit->getCurrency()
                    && $rent->getPrice()->getAmount() === $price->getAmount()
                    && $rent->getPrice()->getCurrency() === $price->getCurrency()
                    && $rent->getDepositReturned()->getAmount() === 0
                    && $rent->getStatus() === RentStatus::draft();
            }));

        ($this->handler)($command);
    }

    public function test_it_throws_bad_request_if_product_not_found(): void
    {
        $rentId    = Uuid::v4();
        $productId = Uuid::v4();
        $renterId  = Uuid::v4();

        $startDate = new \DateTime('2025-01-01 10:00:00 Europe/Madrid');
        $endDate   = new \DateTime('2025-01-10 10:00:00 Europe/Madrid');

        $deposit = new Money(10_000, 'EUR');
        $price   = new Money(50_000, 'EUR');

        $command = new CreateRentCommand(
            $rentId,
            $productId,
            $renterId,
            $startDate,
            $endDate,
            $deposit,
            $price
        );

        $this->rentProductServiceMock
            ->expects($this->once())
            ->method('getProductOwnershipByProductId')
            ->with($productId)
            ->willReturn(null);

        $this->rentAuthorisationServiceMock
            ->expects($this->never())
            ->method('canCreate');

        $this->rentRepositoryMock
            ->expects($this->never())
            ->method('save');

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('rent.create.product.not_found');

        ($this->handler)($command);
    }
}
