<?php

declare(strict_types=1);

namespace App\Company\Application\Command\UpdateCompanyUserRole;

use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateCompanyUserRoleCommand::class)]
class UpdateCompanyUserRoleCommandHandler implements CommandHandler
{
    public function __construct(
        private CompanyUserRepositoryInterface $companyUserRepository,
        private UserGranted $userGranted,
    ) {
    }

    public function __invoke(UpdateCompanyUserRoleCommand|Command $command): void
    {
        if (
            !$this->userGranted->isAdmin() &&
            !$this->userGranted->isAdminAtThisCompany($command->getCompanyId())
        ) {
            throw new UnauthorizedException();
        }

        $companyUser = $this->companyUserRepository->findOneBy([
            'companyId' => $command->getCompanyId(),
            'userId' => $command->getUserId()
        ]);

        if ($companyUser === null) {
            throw new NotFoundException();
        }

        $companyUser->setRole($command->getRole());

        $this->companyUserRepository->save($companyUser);
    }
}
