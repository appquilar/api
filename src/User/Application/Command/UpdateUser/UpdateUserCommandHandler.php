<?php

declare(strict_types=1);

namespace App\User\Application\Command\UpdateUser;

use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\Unauthorized\NotEnoughPermissionsException;
use App\Shared\Infrastructure\Security\UserRole;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\AuthTokenServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateUserCommand::class)]
class UpdateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserGranted $userGranted,
        private AuthTokenServiceInterface $authTokenService,
    ) {
    }

    public function __invoke(UpdateUserCommand|Command $command): void
    {
        $user = $this->userRepository->findById($command->getUserId());

        if (!$user) {
            throw new BadRequestException('User not found.');
        }

        if (
            !$this->userGranted->isAdmin() &&
            $this->userGranted->getUser()->getId() !== $user->getId()
        ) {
            throw new NotEnoughPermissionsException();
        }

        if ($command->getEmail() !== $user->getEmail()) {
            $userWithSameEmail = $this->userRepository->findByEmail($command->getEmail());
            if ($userWithSameEmail !== null && $userWithSameEmail->getId() !== $user->getId()) {
                throw new BadRequestException('Email already exists');
            }
        }

        $user->update(
            $command->getEmail(),
            $command->getFirstName(),
            $command->getLastName()
        );


        if (
            $this->userGranted->isAdmin() &&
            $user->hasDifferentRoles($command->getRoles())
        ) {
            $user->setRoles($command->getRoles());
            $this->authTokenService->revokeTokensByUserId($user->getId());
        }

        $this->userRepository->save($user);
    }
}
