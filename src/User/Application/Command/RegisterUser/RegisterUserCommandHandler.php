<?php

declare(strict_types=1);

namespace App\User\Application\Command\RegisterUser;

use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Command\Command;
use App\User\Application\Event\UserRegistered;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\UserPasswordHasher;
use App\User\Domain\Entity\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: RegisterUserCommand::class)]
class RegisterUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasher $passwordHasher,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(RegisterUserCommand|Command $command): void
    {
        $user = new User(
            $command->userId,
            $command->email,
            $this->passwordHasher->hashPassword($command->password)
        );

        $this->userRepository->save($user);

        $this->eventDispatcher->dispatch(
            new UserRegistered(
                $user->getId(),
                $user->getEmail()
            )
        );
    }
}
