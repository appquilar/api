<?php

declare(strict_types=1);

namespace App\User\Application\Command\RegisterUser;

use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Application\Command\CommandInterface;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\UserPasswordHasher;
use App\User\Domain\Entity\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: RegisterUserCommand::class)]
class RegisterUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasher $passwordHasher
    ) {
    }

    public function __invoke(RegisterUserCommand|CommandInterface $command): void
    {
        $user = new User(
            $command->userId,
            $command->email,
            $this->passwordHasher->hashPassword($command->password)
        );

        $this->userRepository->save($user);
    }
}
