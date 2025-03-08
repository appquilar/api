<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Command\AddUserToCompany;

use App\Company\Application\Command\AddUserToCompany\AddUserToCompanyCommand;
use App\Company\Application\Command\AddUserToCompany\AddUserToCompanyCommandHandler;
use App\Company\Application\Exception\BadRequest\UserAlreadyBelongsToACompanyException;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Application\Service\UserServiceInterface;
use App\Company\Domain\Entity\CompanyUser;
use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\Company\Domain\Entity\CompanyFactory;
use App\Tests\Factories\Company\Domain\Entity\CompanyUserFactory;
use App\Tests\Factories\User\Domain\Entity\UserFactory;
use App\Tests\Unit\UnitTestCase;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

class AddUserToCompanyCommandHandlerTest extends UnitTestCase
{
    private CompanyRepositoryInterface|MockObject $companyRepository;
    private CompanyUserRepositoryInterface|MockObject $companyUserRepository;
    private UserServiceInterface|MockObject $userService;
    private UserGranted|MockObject $userGranted;
    private EventDispatcherInterface|MockObject $eventDispatcher;
    private AddUserToCompanyCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->companyUserRepository = $this->createMock(CompanyUserRepositoryInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->userGranted = $this->createMock(UserGranted::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->handler = new AddUserToCompanyCommandHandler(
            $this->companyRepository,
            $this->companyUserRepository,
            $this->userService,
            $this->userGranted,
            $this->eventDispatcher
        );
    }

    public function testSuccessfulAddUserToCompanyAsOwnerOrAdmin(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $role = CompanyUserRole::CONTRIBUTOR;
        $email = 'newuser@example.com';
        /** @var User $user */
        $user = UserFactory::createOne(['userId' => $userId, 'email' => $email]);
        $company = CompanyFactory::createOne(['companyId' => $companyId]);

        // Assume user does not belong to any company
        $this->companyUserRepository->expects($this->once())
            ->method('findCompanyIdByUserId')
            ->with($userId)
            ->willReturn(null);

        // Return a company instance
        $this->companyRepository->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn($company);

        // User exists
        $this->userService->expects($this->once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn($user);

        // Expect that the repository saves a CompanyUser. We'll check that the object
        // has the proper attributes by using a callback.
        $this->companyUserRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (CompanyUser $companyUser) use ($companyId, $userId, $role, $email) {
                return $companyUser->getCompanyId() === $companyId
                    && $companyUser->getUserId()->equals($userId)
                    && $companyUser->getRole() === $role
                    && $companyUser->getEmail() === $email
                    && $companyUser->getStatus() === CompanyUserStatus::PENDING;
            }));

        // Expect the event dispatcher to be called with a CompanyUserCreated event.
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch');

        $command = new AddUserToCompanyCommand($companyId, $role, $userId, $user->getEmail());
        $this->handler->__invoke($command);
    }

    public function testThrowsExceptionIfUserAlreadyBelongsToACompany(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $email = 'example@test.com';
        $companyUser = CompanyUserFactory::createOne(['companyId' => $companyId, 'userId' => $userId]);

        // Simulate that the user already belongs to a company
        $this->companyUserRepository->expects($this->once())
            ->method('findCompanyIdByUserId')
            ->with($userId)
            ->willReturn($companyUser);

        $command = new AddUserToCompanyCommand($companyId, CompanyUserRole::CONTRIBUTOR, $userId, $email);
        $this->expectException(UserAlreadyBelongsToACompanyException::class);
        $this->handler->__invoke($command);
    }

    public function testThrowsExceptionIfCompanyNotFound(): void
    {
        $companyId = Uuid::v4();
        $email = 'user@example.com';
        $userId = Uuid::v4();

        $this->companyUserRepository->expects($this->once())
            ->method('findCompanyIdByUserId')
            ->with($userId)
            ->willReturn(null);

        $this->companyRepository->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn(null);

        $command = new AddUserToCompanyCommand($companyId, CompanyUserRole::CONTRIBUTOR, $userId, $email);
        $this->expectException(EntityNotFoundException::class);
        $this->handler->__invoke($command);
    }

    public function testThrowsExceptionIfUserNotFound(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $email = 'user@example.com';
        $company = CompanyFactory::createOne(['companyId' => $companyId]);

        $this->companyUserRepository->expects($this->once())
            ->method('findCompanyIdByUserId')
            ->with($userId)
            ->willReturn(null);

        // Assume company exists
        $this->companyRepository->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn($company);

        $this->userService->expects($this->once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn(null);

        $command = new AddUserToCompanyCommand($companyId, CompanyUserRole::CONTRIBUTOR, $userId,$email);
        $this->expectException(EntityNotFoundException::class);
        $this->handler->__invoke($command);
    }

    public function testThrowsUnauthorizedExceptionWhenPermissionLacking(): void
    {
        $companyId = Uuid::v4();
        $email = 'user@example.com';
        $company = CompanyFactory::createOne(['companyId' => $companyId]);

        $this->companyRepository->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn($company);

        $this->userGranted->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->userGranted->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($companyId)
            ->willReturn(false);

        $command = new AddUserToCompanyCommand($companyId, CompanyUserRole::CONTRIBUTOR, null, $email);
        $this->expectException(UnauthorizedException::class);
        $this->handler->__invoke($command);
    }
}
