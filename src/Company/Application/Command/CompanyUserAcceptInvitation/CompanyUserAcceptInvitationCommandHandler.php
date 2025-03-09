<?php

declare(strict_types=1);

namespace App\Company\Application\Command\CompanyUserAcceptInvitation;

use App\Company\Application\Event\NewUserAcceptedInvitation;
use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Application\Service\UserServiceInterface;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CompanyUserAcceptInvitationCommand::class)]
class CompanyUserAcceptInvitationCommandHandler implements CommandHandler
{
    public function __construct(
        private CompanyUserRepositoryInterface $companyUserRepository,
        private UserServiceInterface $userService,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(CompanyUserAcceptInvitationCommand|Command $command): void
    {
        $companyUser = $this->companyUserRepository->findOneBy(['invitationToken' => $command->getInvitationToken()]);

        if (
            $companyUser === null ||
            !$companyUser->getCompanyId()->equals($command->getCompanyId())
        ) {
            throw new UnauthorizedException();
        }

        $newUser = $companyUser->getUserId() === null;

        if ($companyUser->getStatus() === CompanyUserStatus::ACCEPTED) {
            throw new BadRequestException();
        }

        $companyUser->setStatus(CompanyUserStatus::ACCEPTED);

        $this->companyUserRepository->save($companyUser);

        if ($newUser) {
            if ($command->getEmail() === null || $command->getPassword() === null) {
                throw new BadRequestException();
            }
            $this->validateIfUserAlreadyExists($command);

            $this->eventDispatcher->dispatch(
                new NewUserAcceptedInvitation(
                    $command->getEmail(),
                    $command->getPassword()
                )
            );
        }
    }

    private function validateIfUserAlreadyExists(CompanyUserAcceptInvitationCommand|Command $command): void
    {
        $alreadyExistentUser = $this->userService->getUserByEmail($command->getEmail());
        if ($alreadyExistentUser !== null) {
            throw new BadRequestException();
        }
    }
}
