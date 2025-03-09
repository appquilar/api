<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Command\CompanyUserAcceptInvitation;

use App\Company\Application\Command\CompanyUserAcceptInvitation\CompanyUserAcceptInvitationCommand;
use App\Company\Application\Command\CompanyUserAcceptInvitation\CompanyUserAcceptInvitationCommandHandler;
use App\Company\Application\Event\NewUserAcceptedInvitation;
use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Application\Service\UserServiceInterface;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\Company\Domain\Entity\CompanyUserFactory;
use App\Tests\Factories\User\Domain\Entity\UserFactory;
use App\Tests\Unit\UnitTestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

class CompanyUserAcceptInvitationCommandHandlerTest extends UnitTestCase
{
    private CompanyUserRepositoryInterface $companyUserRepository;
    private UserServiceInterface $userService;
    private EventDispatcherInterface $eventDispatcher;
    private CompanyUserAcceptInvitationCommandHandler $handler;

    protected function setUp(): void
    {
        $this->companyUserRepository = $this->createMock(CompanyUserRepositoryInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->handler = new CompanyUserAcceptInvitationCommandHandler(
            $this->companyUserRepository,
            $this->userService,
            $this->eventDispatcher
        );
    }

    public function testThrowsUnauthorizedIfInvalidToken(): void
    {
        $token = 'invalid-token';
        $this->companyUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['invitationToken' => $token])
            ->willReturn(null);

        $this->expectException(UnauthorizedException::class);

        $this->handler->__invoke(
            new CompanyUserAcceptInvitationCommand(Uuid::v4(), $token, 'user@example.com', 'password123')
        );
    }

    public function testThrowsUnauthorizedIfCompanyIdMismatch(): void
    {
        $companyUser = CompanyUserFactory::createOne();
        $token = 'valid-token';

        $this->companyUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['invitationToken' => $token])
            ->willReturn($companyUser);

        $this->expectException(UnauthorizedException::class);

        $this->handler->__invoke(
            new CompanyUserAcceptInvitationCommand(Uuid::v4(), $token, 'user@example.com', 'password123')
        );
    }

    public function testThrowsBadRequestIfAlreadyAccepted(): void
    {
        $companyId = Uuid::v4();
        $companyUser = CompanyUserFactory::createOne(['companyId' => $companyId, 'status' => CompanyUserStatus::ACCEPTED]);
        $token = 'valid-token';

        $this->companyUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['invitationToken' => $token])
            ->willReturn($companyUser);

        $this->expectException(BadRequestException::class);

        $this->handler->__invoke(
            new CompanyUserAcceptInvitationCommand($companyId, $token, 'user@example.com', 'password123')
        );
    }

    public function testSavesCompanyUserAndDispatchesEventForNewUser(): void
    {
        $companyId = Uuid::v4();
        $token = 'valid-token';
        $companyUser = CompanyUserFactory::createOne(['companyId' => $companyId, 'status' => CompanyUserStatus::PENDING, 'userId' => null, 'invitationToken' => $token]);

        $this->companyUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['invitationToken' => $token])
            ->willReturn($companyUser);

        $this->companyUserRepository
            ->expects($this->once())
            ->method('save');

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(NewUserAcceptedInvitation::class));

        $this->handler->__invoke(
            new CompanyUserAcceptInvitationCommand($companyId, $token, 'user@example.com', 'password123')
        );
    }

    public function testThrowsBadRequestIfUserAlreadyExists(): void
    {
        $companyId = Uuid::v4();
        $email = 'user@example.com';
        $token = 'valid-token';
        $companyUser = CompanyUserFactory::createOne(['companyId' => $companyId, 'status' => CompanyUserStatus::PENDING, 'userId' => null]);
        $user = UserFactory::createOne(['email' => $email]);

        $this->companyUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['invitationToken' => $token])
            ->willReturn($companyUser);

        $this->userService
            ->method('getUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->expectException(BadRequestException::class);

        $this->handler->__invoke(
            new CompanyUserAcceptInvitationCommand($companyId, $token, 'user@example.com', 'password123')
        );
    }

    public function testThrowsBadRequestIfEmailOrPasswordIsMissingForNewUser(): void
    {
        $companyId = Uuid::v4();
        $token = 'valid-token';
        $companyUser = CompanyUserFactory::createOne(['companyId' => $companyId, 'status' => CompanyUserStatus::PENDING, 'userId' => null]);

        $this->companyUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['invitationToken' => $token])
            ->willReturn($companyUser);

        $this->expectException(BadRequestException::class);

        $this->handler->__invoke(
            new CompanyUserAcceptInvitationCommand($companyId, $token, null, 'password123')
        );

        $this->expectException(BadRequestException::class);

        $this->handler->__invoke(
            new CompanyUserAcceptInvitationCommand($companyId, 'valid-token', 'user@example.com', null)
        );
    }
}
