<?php

declare(strict_types=1);

namespace App\User\Application\Listener;

use App\Company\Application\Event\NewUserAcceptedInvitation;
use App\Shared\Application\Command\CommandBus;
use App\User\Application\Command\RegisterUser\RegisterUserCommand;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Uid\Uuid;

#[AsEventListener(event: NewUserAcceptedInvitation::class)]
class OnNewUserAcceptedInvitationRegisterUserListener
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(NewUserAcceptedInvitation $event): void
    {
        $this->commandBus->dispatch(
            new RegisterUserCommand(
                Uuid::v4(),
                $event->getEmail(),
                $event->getPassword()
            )
        );
    }
}
