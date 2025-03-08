<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Listener;

use App\Company\Application\Command\AddUserToCompany\AddUserToCompanyCommand;
use App\Company\Application\Event\CompanyCreated;
use App\Company\Application\Listener\OnCompanyCreatedAddOwnerListener;
use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Shared\Application\Command\CommandBus;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class OnCompanyCreatedAddOwnerListenerTest extends UnitTestCase
{
    private CommandBus|MockObject $commandBusMock;
    private OnCompanyCreatedAddOwnerListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBusMock = $this->createMock(CommandBus::class);
        $this->listener = new OnCompanyCreatedAddOwnerListener($this->commandBusMock);
    }

    public function testOnCompanyCreatedAddOwnerListener(): void
    {
        $email = 'user@test.com';
        $event = new CompanyCreated(Uuid::v4(), Uuid::v4(), $email);
        $command = new AddUserToCompanyCommand(
            $event->getCompanyId(),
            CompanyUserRole::ADMIN,
            $event->getOwnerId(),
            $email,
            CompanyUserStatus::ACCEPTED
        );

        $this->commandBusMock->expects($this->once())
            ->method('dispatch')
            ->with($command);

        $this->listener->__invoke($event);
    }
}
