<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Command\RemoveCompanyUser;

use App\Company\Application\Command\RemoveCompanyUser\RemoveCompanyUserCommand;
use App\Company\Application\Command\RemoveCompanyUser\RemoveCompanyUserCommandHandler;
use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\Company\Domain\Entity\CompanyUserFactory;
use App\Tests\Factories\User\Domain\Entity\UserFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class RemoveCompanyUserCommandHandlerTest extends UnitTestCase
{
    private CompanyUserRepositoryInterface|MockObject $companyUserRepositoryMock;
    private UserGranted|MockObject $userGrantedMock;
    private RemoveCompanyUserCommandHandler|MockObject $handler;

    protected function setUp(): void
    {
        $this->companyUserRepositoryMock = $this->createMock(CompanyUserRepositoryInterface::class);
        $this->userGrantedMock = $this->createMock(UserGranted::class);
        $this->handler = new RemoveCompanyUserCommandHandler($this->companyUserRepositoryMock, $this->userGrantedMock);
    }

    public function testThrowsUnauthorizedIfNotAdmin(): void
    {
        $companyId = Uuid::v4();
        $command = new RemoveCompanyUserCommand($companyId, Uuid::v4());

        $this->userGrantedMock->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);
        $this->userGrantedMock->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($companyId)
            ->willReturn(false);

        $this->expectException(UnauthorizedException::class);
        $this->handler->__invoke($command);
    }

    public function testThrowsBadRequestIfUserTriesToRemoveThemselves(): void
    {
        $userId = Uuid::v4();
        $command = new RemoveCompanyUserCommand(Uuid::v4(), $userId);
        $user = UserFactory::createOne(['userId' => $userId]);

        $this->userGrantedMock
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(true);

        $this->userGrantedMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->expectException(BadRequestException::class);
        $this->handler->__invoke($command);
    }

    public function testThrowsNotFoundIfUserNotInCompany(): void
    {
        $command = new RemoveCompanyUserCommand(Uuid::v4(), Uuid::v4());
        $user = UserFactory::createOne();

        $this->userGrantedMock
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);
        $this->userGrantedMock->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($command->getCompanyId())
            ->willReturn(true);
        $this->userGrantedMock->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->companyUserRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'companyId' => $command->getCompanyId(),
                'userId' => $command->getUserId()
            ])
            ->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->handler->__invoke($command);
    }

    public function testSuccessfullyRemovesUserFromCompany(): void
    {
        $userId = Uuid::v4();
        $companyId = Uuid::v4();
        $companyUser = CompanyUserFactory::createOne(['companyId' => $companyId, 'userId' => $userId]);
        $user = UserFactory::createOne();
        $command = new RemoveCompanyUserCommand($companyId, $userId);

        $this->userGrantedMock
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);
        $this->userGrantedMock->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($command->getCompanyId())
            ->willReturn(true);
        $this->userGrantedMock->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->companyUserRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'companyId' => $command->getCompanyId(),
                'userId' => $command->getUserId()
            ])
            ->willReturn($companyUser);

        $this->handler->__invoke($command);
    }
}
