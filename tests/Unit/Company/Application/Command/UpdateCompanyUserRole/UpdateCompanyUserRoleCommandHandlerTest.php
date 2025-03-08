<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Command\UpdateCompanyUserRole;

use App\Company\Application\Command\UpdateCompanyUserRole\UpdateCompanyUserRoleCommand;
use App\Company\Application\Command\UpdateCompanyUserRole\UpdateCompanyUserRoleCommandHandler;
use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Domain\Entity\CompanyUser;
use App\Company\Domain\Enum\CompanyUserRole;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\Company\Domain\Entity\CompanyUserFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCompanyUserRoleCommandHandlerTest extends UnitTestCase
{
    private CompanyUserRepositoryInterface $companyUserRepository;
    private UserGranted $userGranted;
    private UpdateCompanyUserRoleCommandHandler $handler;

    protected function setUp(): void
    {
        $this->companyUserRepository = $this->createMock(CompanyUserRepositoryInterface::class);
        $this->userGranted = $this->createMock(UserGranted::class);
        $this->handler = new UpdateCompanyUserRoleCommandHandler(
            $this->companyUserRepository,
            $this->userGranted
        );
    }

    public function testThrowsUnauthorizedExceptionWhenUserIsNotAdmin(): void
    {
        $command = new UpdateCompanyUserRoleCommand(
            Uuid::v4(),
            Uuid::v4(),
            CompanyUserRole::CONTRIBUTOR
        );

        $this->userGranted->method('isAdmin')->willReturn(false);
        $this->userGranted->method('isAdminAtThisCompany')->willReturn(false);

        $this->expectException(UnauthorizedException::class);

        $this->handler->__invoke($command);
    }

    public function testThrowsNotFoundExceptionWhenCompanyUserDoesNotExist(): void
    {
        $command = new UpdateCompanyUserRoleCommand(
            Uuid::v4(),
            Uuid::v4(),
            CompanyUserRole::ADMIN
        );

        $this->userGranted->method('isAdmin')->willReturn(true);

        $this->companyUserRepository->method('findOneBy')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->handler->__invoke($command);
    }

    public function testUpdatesUserRoleSuccessfullyAsAdmin(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $command = new UpdateCompanyUserRoleCommand(
            $companyId,
            $userId,
            CompanyUserRole::ADMIN
        );
        $companyUser = CompanyUserFactory::createOne(['companyId' => $companyId, 'userId' => $userId, 'role' => CompanyUserRole::CONTRIBUTOR]);

        $this->userGranted->method('isAdmin')->willReturn(true);

        $this->companyUserRepository->method('findOneBy')
            ->with(['companyId' => $companyId, 'userId' => $userId])
            ->willReturn($companyUser);

        $this->companyUserRepository->expects($this->once())->method('save')->with($companyUser);

        $this->handler->__invoke($command);
    }

    public function testUpdatesUserRoleSuccessfullyAsCompanyAdmin(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $command = new UpdateCompanyUserRoleCommand(
            $companyId,
            $userId,
            CompanyUserRole::ADMIN
        );
        $companyUser = CompanyUserFactory::createOne(['companyId' => $companyId, 'userId' => $userId, 'role' => CompanyUserRole::CONTRIBUTOR]);

        $this->userGranted->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->userGranted->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($companyId)
            ->willReturn(true);

        $this->companyUserRepository->method('findOneBy')
            ->with(['companyId' => $companyId, 'userId' => $userId])
            ->willReturn($companyUser);

        $this->companyUserRepository->expects($this->once())->method('save')->with($companyUser);

        $this->handler->__invoke($command);
    }
}
