<?php

declare(strict_types=1);

namespace App\User\Application\Command\ChangePasswordFromToken;

use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\User\Application\Repository\ForgotPasswordTokenRepositoryInterface;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\UserPasswordHasher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: ChangePasswordFromTokenCommand::class)]
class ChangePasswordFromTokenCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ForgotPasswordTokenRepositoryInterface $forgotPasswordTokenRepository,
        private UserPasswordHasher $passwordHasher,
    ) {
    }

    public function __invoke(ChangePasswordFromTokenCommand|Command $command): void
    {
        $user = $this->userRepository->findByEmail($command->getEmail());
        if ($user === null) {
            throw new UnauthorizedException('User not found');
        }

        $forgotPasswordToken = $this->forgotPasswordTokenRepository->getToken($command->getToken());
        if ($forgotPasswordToken === null || $forgotPasswordToken->isExpired()) {
            throw new UnauthorizedException('Invalid token');
        }
        
        $user->setPassword($this->passwordHasher->hashPassword($command->getPassword()));
        $this->userRepository->save($user);

        $this->forgotPasswordTokenRepository->deleteToken($forgotPasswordToken);
    }
}
