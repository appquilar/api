<?php

declare(strict_types=1);

namespace App\Company\Application\Command\AddUserToCompany;

use App\Company\Application\Event\CompanyUserCreated;
use App\Company\Application\Exception\BadRequest\UserAlreadyBelongsToACompanyException;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Application\Service\UserServiceInterface;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\CompanyUser;
use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\User\Domain\Entity\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler(handles: AddUserToCompanyCommand::class)]
class AddUserToCompanyCommandHandler implements CommandHandler
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private CompanyUserRepositoryInterface $companyUserRepository,
        private UserServiceInterface $userService,
        private UserGranted $userGranted,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(AddUserToCompanyCommand|Command $command): void
    {
        $this->validateUserDoesntAlreadyBelongToCompany($command->getUserId());
        $company = $this->getCompany($command);
        $user = $this->getUser($command);

        if (
            $user === null &&
            !$this->userGranted->isAdmin() &&
            !$this->userGranted->isAdminAtThisCompany($company->getId())
        ) {
            throw new UnauthorizedException();
        }

        $companyUser = new CompanyUser(
            $company->getId(),
            $command->getRole(),
            $command->getEmail(),
            $user?->getId(),
            $command->getStatus()
        );

        $this->companyUserRepository->save($companyUser);

        $this->eventDispatcher->dispatch(
            new CompanyUserCreated(
                $command->getCompanyId(),
                $command->getEmail(),
                $this->isOwner($command),
                $companyUser->getInvitationToken()
            )
        );
    }

    private function isOwner(AddUserToCompanyCommand $command): bool
    {
        return $command->getUserId() !== null &&
            $command->getRole() === CompanyUserRole::ADMIN &&
            $command->getStatus() === CompanyUserStatus::ACCEPTED;
    }

    private function getUser(Command|AddUserToCompanyCommand $command): ?User
    {
        if ($command->getUserId() === null) {
            return null;
        }

        $user = $this->userService->getUserById($command->getUserId());
        if ($user === null) {
            throw new EntityNotFoundException($command->getUserId());
        }

        return $user;
    }

    private function getCompany(Command|AddUserToCompanyCommand $command): Company
    {
        $company = $this->companyRepository->findById($command->getCompanyId());

        if ($company === null) {
            throw new EntityNotFoundException($command->getCompanyId());
        }

        return $company;
    }

    private function validateUserDoesntAlreadyBelongToCompany(?Uuid $userId): void
    {
        if ($userId === null) {
            return;
        }
        $companyUser = $this->companyUserRepository->findCompanyIdByUserId($userId);
        if ($companyUser !== null) {
            throw new UserAlreadyBelongsToACompanyException();
        }
    }
}
