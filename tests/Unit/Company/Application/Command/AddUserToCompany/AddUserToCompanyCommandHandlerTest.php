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
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\Company\Domain\Entity\CompanyFactory;
use App\Tests\Factories\Company\Domain\Entity\CompanyUserFactory;
use App\Tests\Factories\User\Domain\Entity\UserFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class AddUserToCompanyCommandHandlerTest extends UnitTestCase
{
    private CompanyRepositoryInterface|MockObject $companyRepository;
    private CompanyUserRepositoryInterface|MockObject $companyUserRepository;
    private UserServiceInterface|MockObject $userService;
    private UserGranted|MockObject $userGranted;
    private AddUserToCompanyCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->companyUserRepository = $this->createMock(CompanyUserRepositoryInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->userGranted = $this->createMock(UserGranted::class);

        $this->handler = new AddUserToCompanyCommandHandler(
            $this->companyRepository,
            $this->companyUserRepository,
            $this->userService,
            $this->userGranted
        );
    }

    public function testSuccessfullyAddsUserToCompany(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $role = CompanyUserRole::CONTRIBUTOR;
        $company = CompanyFactory::createOne(['companyId' => $companyId]);
        $user = UserFactory::createOne(['userId' => $userId]);

        $this->companyUserRepository->expects($this->once())
            ->method('findCompanyIdByUserId')
            ->with($userId)
            ->willReturn(null);

        $this->companyRepository->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn($company);

        $this->userService->expects($this->once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn($user);

        $this->userGranted->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($companyId)
            ->willReturn(true);

        $this->companyUserRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (CompanyUser $companyUser) use ($companyId, $userId, $role) {
                return $companyUser->getCompanyId() === $companyId &&
                    $companyUser->getUserId() === $userId &&
                    $companyUser->getCompanyUserRole() === $role;
            }));

        $command = new AddUserToCompanyCommand($userId, $companyId, $role, false);
        $this->handler->__invoke($command);
    }

    public function testThrowsExceptionWhenUserAlreadyBelongsToCompany(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $companyUser = CompanyUserFactory::createOne(['companyId' => $companyId, 'userId' => $userId]);

        $this->companyUserRepository->expects($this->once())
            ->method('findCompanyIdByUserId')
            ->with($userId)
            ->willReturn($companyUser);

        $this->expectException(UserAlreadyBelongsToACompanyException::class);

        $command = new AddUserToCompanyCommand($userId, $companyId, CompanyUserRole::CONTRIBUTOR, false);
        $this->handler->__invoke($command);
    }

    public function testThrowsExceptionWhenCompanyNotFound(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();

        $this->companyUserRepository->expects($this->once())
            ->method('findCompanyIdByUserId')
            ->with($userId)
            ->willReturn(null);

        $this->companyRepository->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);

        $command = new AddUserToCompanyCommand($userId, $companyId, CompanyUserRole::CONTRIBUTOR, false);
        $this->handler->__invoke($command);
    }

    public function testThrowsUnauthorizedExceptionWhenUserLacksPermissions(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $company = CompanyFactory::createOne(['companyId' => $companyId]);
        $user = UserFactory::createOne(['userId' => $userId]);

        $this->companyUserRepository->expects($this->once())
            ->method('findCompanyIdByUserId')
            ->with($userId)
            ->willReturn(null);

        $this->companyRepository->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn($company);

        $this->userService->expects($this->once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn($user);

        $this->userGranted->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($companyId)
            ->willReturn(false);

        $this->userGranted->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->expectException(UnauthorizedException::class);

        $command = new AddUserToCompanyCommand($userId, $companyId, CompanyUserRole::CONTRIBUTOR, false);
        $this->handler->__invoke($command);
    }
}
