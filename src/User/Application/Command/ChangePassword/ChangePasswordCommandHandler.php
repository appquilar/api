<?php

declare(strict_types=1);

namespace App\User\Application\Command\ChangePassword;

use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\UserPasswordHasher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: ChangePasswordCommand::class)]
class ChangePasswordCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasher      $passwordHasher,
        private UserGranted             $userGranted
    ) {
    }

    public function __invoke(ChangePasswordCommand|Command $command): void
    {
        $user = $this->userGranted->getUser();
        if (!$this->passwordHasher->verifyPassword(
            $command->getOldPassword(),
            $user->getPassword()
        )) {
            throw new UnauthorizedException();
        }

        $user->setPassword(
            $this->passwordHasher->hashPassword($command->getNewPassword())
        );

        $this->userRepository->save($user);
        $this->userGranted->setUser($user);
    }
}
