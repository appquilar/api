<?php declare(strict_types=1);

namespace App\Tests\Unit\Rent\Application\Service;

use App\Rent\Application\Service\RentAuthorisationService;
use App\Rent\Application\Service\RentCompanyUserServiceInterface;
use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentOwnerType;
use App\Rent\Domain\Enum\RentStatus;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Unit\UnitTestCase;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class RentAuthorisationServiceTest extends UnitTestCase
{
    /** @var UserGranted|MockObject */
    private UserGranted|MockObject $userGranted;

    /** @var RentCompanyUserServiceInterface|MockObject */
    private RentCompanyUserServiceInterface|MockObject $rentCompanyUserService;

    private RentAuthorisationService $authorizationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userGranted = $this->createMock(UserGranted::class);
        $this->rentCompanyUserService = $this->createMock(RentCompanyUserServiceInterface::class);

        $this->authorizationService = new RentAuthorisationService(
            $this->userGranted,
            $this->rentCompanyUserService
        );
    }

    public function test_can_create_allows_when_user_is_renter(): void
    {
        $userId   = Uuid::v4();
        $ownerId  = Uuid::v4();
        $renterId = $userId;

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getRenterId')->willReturn($renterId);
        $rent->method('getOwnerId')->willReturn($ownerId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::USER);

        // Como el usuario es el renter, no debería llamar a userBelongsToCompany
        $this->rentCompanyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        // Si no lanza excepción, el test pasa
        $this->authorizationService->canCreate($rent);
        $this->assertTrue(true);
    }

    public function test_can_create_allows_when_user_is_owner_user_type(): void
    {
        $userId   = Uuid::v4();
        $ownerId  = $userId;
        $renterId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getRenterId')->willReturn($renterId);
        $rent->method('getOwnerId')->willReturn($ownerId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::USER);

        $this->rentCompanyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        $this->authorizationService->canCreate($rent);
        $this->assertTrue(true);
    }

    public function test_can_create_allows_when_user_belongs_to_owner_company(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();
        $renterId  = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getRenterId')->willReturn($renterId);
        $rent->method('getOwnerId')->willReturn($companyId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::COMPANY);

        $this->rentCompanyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(true);

        $this->authorizationService->canCreate($rent);
        $this->assertTrue(true);
    }

    public function test_can_create_throws_when_user_is_neither_owner_nor_renter(): void
    {
        $userId    = Uuid::v4();
        $ownerId   = Uuid::v4();
        $renterId  = Uuid::v4();
        $companyId = $ownerId;

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getRenterId')->willReturn($renterId);
        $rent->method('getOwnerId')->willReturn($companyId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::COMPANY);

        $this->rentCompanyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(false);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('rent.user.cannot_create');

        $this->authorizationService->canCreate($rent);
    }

    public function test_can_edit_allows_when_user_is_owner_user_type(): void
    {
        $userId  = Uuid::v4();
        $ownerId = $userId;

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getOwnerId')->willReturn($ownerId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::USER);

        $this->rentCompanyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        $this->authorizationService->canEdit($rent);
        $this->assertTrue(true);
    }

    public function test_can_edit_allows_when_user_belongs_to_owner_company(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getOwnerId')->willReturn($companyId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::COMPANY);

        $this->rentCompanyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(true);

        $this->authorizationService->canEdit($rent);
        $this->assertTrue(true);
    }

    public function test_can_edit_throws_when_user_is_not_owner(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getOwnerId')->willReturn($companyId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::COMPANY);

        $this->rentCompanyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(false);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('rent.user.cannot_edit');

        $this->authorizationService->canEdit($rent);
    }

    public function test_can_change_status_to_cancelled_allows_when_user_is_renter(): void
    {
        $userId   = Uuid::v4();
        $ownerId  = Uuid::v4();
        $renterId = $userId;

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getRenterId')->willReturn($renterId);
        $rent->method('getOwnerId')->willReturn($ownerId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::USER);

        $this->rentCompanyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        $this->authorizationService->canChangeStatus($rent, RentStatus::CANCELLED);
        $this->assertTrue(true);
    }

    public function test_can_change_status_to_cancelled_throws_when_user_is_neither_owner_nor_renter(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();
        $renterId  = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getRenterId')->willReturn($renterId);
        $rent->method('getOwnerId')->willReturn($companyId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::COMPANY);

        $this->rentCompanyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(false);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('rent.user.cannot_cancel');

        $this->authorizationService->canChangeStatus($rent, RentStatus::CANCELLED);
    }

    public function test_can_change_status_non_cancelled_requires_owner(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getOwnerId')->willReturn($companyId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::COMPANY);

        $this->rentCompanyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(true);

        $this->authorizationService->canChangeStatus($rent, RentStatus::PENDING); // o el estado que tengas
        $this->assertTrue(true);
    }

    public function test_can_change_status_non_cancelled_throws_when_not_owner(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getOwnerId')->willReturn($companyId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::COMPANY);

        $this->rentCompanyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(false);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('rent.user.cannot_change_state');

        $this->authorizationService->canChangeStatus($rent, RentStatus::PENDING);
    }

    // ---------------------------------------------------------
    // canChangePrice
    // ---------------------------------------------------------

    public function test_can_change_price_allows_when_owner(): void
    {
        $userId  = Uuid::v4();
        $ownerId = $userId;

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getOwnerId')->willReturn($ownerId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::USER);

        $this->rentCompanyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        $this->authorizationService->canChangePrice($rent);
        $this->assertTrue(true);
    }

    public function test_can_change_price_throws_when_not_owner(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getOwnerId')->willReturn($companyId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::COMPANY);

        $this->rentCompanyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(false);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('rent.user.cannot_change_price');

        $this->authorizationService->canChangePrice($rent);
    }

    public function test_can_view_when_user_is_renter(): void
    {
        $userId   = Uuid::v4();
        $ownerId  = Uuid::v4();
        $renterId = $userId;

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getRenterId')->willReturn($renterId);
        $rent->method('getOwnerId')->willReturn($ownerId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::USER);

        $this->rentCompanyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        $this->authorizationService->canView($rent);
        $this->assertTrue(true);
    }

    public function test_can_view_when_user_is_owner_user_type(): void
    {
        $userId  = Uuid::v4();
        $ownerId = $userId;
        $renterId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getRenterId')->willReturn($renterId);
        $rent->method('getOwnerId')->willReturn($ownerId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::USER);

        $this->rentCompanyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        $this->authorizationService->canView($rent);
        $this->assertTrue(true);
    }

    public function test_can_view_when_user_belongs_to_company_owner(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();
        $renterId  = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getRenterId')->willReturn($renterId);
        $rent->method('getOwnerId')->willReturn($companyId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::COMPANY);

        $this->rentCompanyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(true);

        $this->authorizationService->canView($rent);
        $this->assertTrue(true);
    }

    public function test_can_view_throws_when_user_is_neither_owner_nor_renter(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();
        $renterId  = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);
        $rent->method('getRenterId')->willReturn($renterId);
        $rent->method('getOwnerId')->willReturn($companyId);
        $rent->method('getOwnerType')->willReturn(RentOwnerType::COMPANY);

        $this->rentCompanyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(false);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('rent.user.cannot_view');

        $this->authorizationService->canView($rent);
    }
}
