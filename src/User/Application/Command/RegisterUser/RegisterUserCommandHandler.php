<?php

declare(strict_types=1);

namespace App\User\Application\Command\RegisterUser;

use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Application\Command\CommandInterface;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Domain\Entity\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler(handles: RegisterUserCommand::class)]
class RegisterUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(RegisterUserCommand|CommandInterface $command): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            new User(
                $command->userId,
                $command->email,
                ''
            ),
            $command->password
        );
        $user = new User(
            $command->userId,
            $command->email,
            $hashedPassword
        );

        $this->userRepository->save($user);
    }
}
