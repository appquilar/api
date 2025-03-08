<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Query\GetCompanyUsers;

use App\Company\Application\Query\GetCompanyUsers\GetCompanyUsersQuery;
use App\Company\Application\Query\GetCompanyUsers\GetCompanyUsersQueryHandler;
use App\Company\Application\Query\GetCompanyUsers\GetCompanyUsersQueryResult;
use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Application\Transformer\CompanyUserTransformer;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\Company\Domain\Entity\CompanyUserFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class GetCompanyUsersQueryHandlerTest extends UnitTestCase
{
    private CompanyUserRepositoryInterface|MockObject $companyUserRepositoryMock;
    private UserGranted|MockObject $userGrantedMock;
    private CompanyUserTransformer|MockObject $companyUserTransformerMock;
    private GetCompanyUsersQueryHandler $handler;

    public function setUp(): void
    {
        parent::setUp();

        $this->companyUserRepositoryMock = $this->createMock(CompanyUserRepositoryInterface::class);
        $this->userGrantedMock = $this->createMock(UserGranted::class);
        $this->companyUserTransformerMock = $this->createMock(CompanyUserTransformer::class);

        $this->handler = new GetCompanyUsersQueryHandler(
            $this->companyUserRepositoryMock,
            $this->userGrantedMock,
            $this->companyUserTransformerMock
        );
    }

    public function testThrowsUnauthorizedExceptionIfUserDoesNotWorkAtCompany(): void
    {
        $companyId = Uuid::v4();
        $query = new GetCompanyUsersQuery($companyId, 1, 10);

        $this->userGrantedMock->expects($this->once())
            ->method('getCompanyUser')
            ->willReturn(null);

        $this->expectException(UnauthorizedException::class);
        $this->handler->__invoke($query);
    }

    public function testThrowsUnauthorizedExceptionIfUserLacksPermissions(): void
    {
        $companyId = Uuid::v4();
        $query = new GetCompanyUsersQuery($companyId, 1, 10);

        $this->userGrantedMock->expects($this->once())
            ->method('getCompanyUser')
            ->willReturn(null);

        $this->userGrantedMock->expects($this->once())
            ->method('worksAtThisCompany')
            ->with($companyId)
            ->willReturn(false);

        $this->userGrantedMock->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->expectException(UnauthorizedException::class);
        $this->handler->__invoke($query);
    }

    public function testReturnsCompanyUsersSuccessfully(): void
    {
        $companyId = Uuid::v4();
        $query = new GetCompanyUsersQuery($companyId, 1, 10);

        $companyUsers = CompanyUserFactory::createMany(2, ['companyId' => $companyId]);

        $transformedUsers = [
            ['id' => '123', 'role' => 'admin'],
            ['id' => '456', 'role' => 'contributor']
        ];

        $this->userGrantedMock->expects($this->once())
            ->method('getCompanyUser')
            ->willReturn($companyUsers[0]);

        $this->companyUserRepositoryMock->expects($this->once())
            ->method('findPaginatedUsersByCompanyId')
            ->with($companyId, 1, 10)
            ->willReturn($companyUsers);

        $this->companyUserTransformerMock->expects($this->exactly(2))
            ->method('transform')
            ->willReturnOnConsecutiveCalls($transformedUsers[0], $transformedUsers[1]);

        $this->companyUserRepositoryMock->expects($this->once())
            ->method('countUsersByCompanyId')
            ->with($companyId)
            ->willReturn(2);

        /** @var GetCompanyUsersQueryResult $result */
        $result = $this->handler->__invoke($query);

        $this->assertInstanceOf(GetCompanyUsersQueryResult::class, $result);
        $this->assertEquals($transformedUsers, $result->getData());
        $this->assertEquals(2, $result->getResponseData()['total']);
        $this->assertEquals(1, $result->getResponseData()['page']);
    }

    public function testReturnCompanyUsersEmpty(): void
    {
        $companyId = Uuid::v4();
        $query = new GetCompanyUsersQuery($companyId, 1, 10);

        $companyUsers = [];

        $transformedUsers = [];

        $this->userGrantedMock->expects($this->once())
            ->method('getCompanyUser')
            ->willReturn(null);

        $this->userGrantedMock->expects($this->once())
            ->method('worksAtThisCompany')
            ->with($companyId)
            ->willReturn(false);

        $this->userGrantedMock->expects($this->once())
            ->method('isAdmin')
            ->willReturn(true);

        $this->companyUserRepositoryMock->expects($this->once())
            ->method('findPaginatedUsersByCompanyId')
            ->with($companyId, 1, 10)
            ->willReturn($companyUsers);

        $this->companyUserRepositoryMock->expects($this->once())
            ->method('countUsersByCompanyId')
            ->with($companyId)
            ->willReturn(0);

        /** @var GetCompanyUsersQueryResult $result */
        $result = $this->handler->__invoke($query);

        $this->assertInstanceOf(GetCompanyUsersQueryResult::class, $result);
        $this->assertEquals($transformedUsers, $result->getData());
        $this->assertEquals(0, $result->getResponseData()['total']);
        $this->assertEquals(1, $result->getResponseData()['page']);
    }
}
