<?php

declare(strict_types=1);

namespace App\Company\Application\Command\AddUserToCompany;

use App\Company\Application\Exception\BadRequest\UserAlreadyBelongsToACompanyException;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Application\Service\UserServiceInterface;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\CompanyUser;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\User\Domain\Entity\User;
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
    ) {
    }

    public function __invoke(AddUserToCompanyCommand|Command $command): void
    {
        $this->validateUserDoesntAlreadyBelongToCompany($command->getUserId());
        $company = $this->getCompany($command);
        $user = $this->getUser($command);

        if (
            !$command->isOwner() &&
            !$this->userGranted->isAdmin() &&
            !$this->userGranted->isAdminAtThisCompany($company->getId())
        ) {
            throw new UnauthorizedException();
        }

        $companyUser = new CompanyUser(
            $company->getId(),
            $user->getId(),
            $command->getRole()
        );

        $this->companyUserRepository->save($companyUser);
    }

    private function getUser(Command|AddUserToCompanyCommand $command): User
    {
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

    private function validateUserDoesntAlreadyBelongToCompany(Uuid $userId): void
    {
        $companyUser = $this->companyUserRepository->findCompanyIdByUserId($userId);
        if ($companyUser !== null) {
            throw new UserAlreadyBelongsToACompanyException();
        }
    }
}
