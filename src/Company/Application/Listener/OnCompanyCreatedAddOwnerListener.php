<?php

declare(strict_types=1);

namespace App\Company\Application\Listener;

use App\Company\Application\Command\AddUserToCompany\AddUserToCompanyCommand;
use App\Company\Application\Event\CompanyCreated;
use App\Company\Domain\Enum\CompanyUserRole;
use App\Shared\Application\Command\CommandBus;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: CompanyCreated::class)]
class OnCompanyCreatedAddOwnerListener
{

    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(CompanyCreated $event): void
    {
        $this->commandBus->dispatch(
            new AddUserToCompanyCommand(
                $event->getCompanyId(),
                CompanyUserRole::ADMIN,
                $event->getOwnerId(),
                $event->getOwnerEmail()
            )
        );
    }
}
