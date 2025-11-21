<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Listener;

use App\Company\Application\Event\CompanyCreated;
use App\Product\Application\Command\MigrateOwnershipFromUserToCompany\MigrateOwnershipFromUserToCompanyCommand;
use App\Product\Application\Listener\OnCompanyCreateChangeProductsOwnership;
use App\Shared\Application\Command\CommandBus;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Uid\Uuid;

class OnCompanyCreateChangeProductsOwnershipTest extends UnitTestCase
{
    /** @var CommandBus|MockObject */
    private CommandBus|MockObject $commandBus;

    private OnCompanyCreateChangeProductsOwnership $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = $this->createMock(CommandBus::class);

        $this->listener = new OnCompanyCreateChangeProductsOwnership(
            $this->commandBus
        );
    }

    public function test_it_dispatches_migrate_ownership_command_on_company_created_event(): void
    {
        $ownerId   = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var CompanyCreated|MockObject $event */
        $event = $this->createMock(CompanyCreated::class);
        $event->method('getOwnerId')->willReturn($ownerId);
        $event->method('getCompanyId')->willReturn($companyId);

        $this->commandBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (MigrateOwnershipFromUserToCompanyCommand $command) use ($ownerId, $companyId): bool {
                return $command->getUserId()->equals($ownerId)
                    && $command->getCompanyId()->equals($companyId);
            }));

        ($this->listener)($event);
    }

    public function test_it_propagates_exception_when_command_bus_fails(): void
    {
        $ownerId   = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var CompanyCreated|MockObject $event */
        $event = $this->createMock(CompanyCreated::class);
        $event->method('getOwnerId')->willReturn($ownerId);
        $event->method('getCompanyId')->willReturn($companyId);

        $exception = $this->createMock(ExceptionInterface::class);

        $this->commandBus
            ->expects($this->once())
            ->method('dispatch')
            ->willThrowException($exception);

        $this->expectException(ExceptionInterface::class);

        ($this->listener)($event);
    }
}
