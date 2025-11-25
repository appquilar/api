<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Domain\Service;

use App\Product\Application\Service\ProductAuthorizationService;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Service\CompanyUserServiceInterface;
use App\Product\Domain\Service\ProductAuthorizationServiceInterface;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Unit\UnitTestCase;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class ProductAuthorizationServiceTest extends UnitTestCase
{
    /** @var CompanyUserServiceInterface|MockObject */
    private CompanyUserServiceInterface|MockObject $companyUserService;

    /** @var UserGranted|MockObject */
    private UserGranted|MockObject $userGranted;

    private ProductAuthorizationServiceInterface $authorizationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyUserService = $this->createMock(CompanyUserServiceInterface::class);
        $this->userGranted = $this->createMock(UserGranted::class);

        $this->authorizationService = new ProductAuthorizationService(
            $this->companyUserService,
            $this->userGranted
        );
    }

    public function test_can_view_allows_when_user_is_owner_of_product(): void
    {
        $userId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $product->method('belongsToUser')->willReturn(true);
        $product->method('getUserId')->willReturn($userId);
        $product->expects($this->never())->method('belongsToCompany');

        $this->companyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        $this->authorizationService->canView($product, 'error.message');

        $this->assertTrue(true); // si no se lanza excepción, el test pasa
    }

    public function test_can_view_throws_when_no_user_granted(): void
    {
        $this->userGranted
            ->method('getUser')
            ->willReturn(null);

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $product->expects($this->never())->method('belongsToUser');
        $product->expects($this->never())->method('belongsToCompany');

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('error.message');

        $this->authorizationService->canView($product, 'error.message');
    }

    public function test_can_view_allows_when_product_belongs_to_company_and_user_belongs_to_company(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $product->method('belongsToUser')->willReturn(false);
        $product->method('belongsToCompany')->willReturn(true);
        $product->method('getCompanyId')->willReturn($companyId);

        $this->companyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(true);

        $this->authorizationService->canView($product, 'error.message');

        $this->assertTrue(true);
    }

    public function test_can_view_throws_when_user_not_in_company(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $product->method('belongsToUser')->willReturn(false);
        $product->method('belongsToCompany')->willReturn(true);
        $product->method('getCompanyId')->willReturn($companyId);

        $this->companyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(false);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('error.message');

        $this->authorizationService->canView($product, 'error.message');
    }

    public function test_can_edit_uses_same_permission_logic_as_can_view(): void
    {
        $userId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn($user);

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);
        $product->method('belongsToUser')->willReturn(true);
        $product->method('getUserId')->willReturn($userId);

        $this->companyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        $this->authorizationService->canEdit($product, 'product.update.unauthorized');

        $this->assertTrue(true);
    }

    public function test_cannot_edit_throw_exception(): void
    {
        $userId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->method('getUser')
            ->willReturn(null);

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);
        $product->method('belongsToUser')->willReturn(true);
        $product->method('getUserId')->willReturn($userId);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('product.update.unauthorized');

        $this->authorizationService->canEdit($product, 'product.update.unauthorized');
    }

    public function test_can_view_if_public_allows_when_product_is_published(): void
    {
        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $product->method('isPublished')->willReturn(true);

        $this->authorizationService->canViewIfPublic($product, 'error.message');

        $this->assertTrue(true);
    }

    public function test_can_view_if_public_throws_when_product_is_not_published(): void
    {
        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $product->method('isPublished')->willReturn(false);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('error.message');

        $this->authorizationService->canViewIfPublic($product, 'error.message');
    }

    public function test_assign_ownership_sets_company_when_user_belongs_to_company(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $this->companyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(true);

        $product
            ->expects($this->once())
            ->method('setCompanyId')
            ->with($companyId);

        $product
            ->expects($this->never())
            ->method('setUserId');

        $this->authorizationService->assignOwnership($product, $companyId);
    }

    public function test_assign_ownership_throws_when_company_given_and_user_not_in_company(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $this->companyUserService
            ->expects($this->once())
            ->method('userBelongsToCompany')
            ->with($userId, $companyId)
            ->willReturn(false);

        $product
            ->expects($this->never())
            ->method('setCompanyId');

        $product
            ->expects($this->never())
            ->method('setUserId');

        $this->expectException(UnauthorizedException::class);

        $this->authorizationService->assignOwnership($product, $companyId);
    }

    public function test_assign_ownership_sets_user_when_no_company_provided(): void
    {
        $userId = Uuid::v4();

        /** @var User|MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->userGranted
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $this->companyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        $product
            ->expects($this->once())
            ->method('setUserId')
            ->with($userId);

        $product
            ->expects($this->never())
            ->method('setCompanyId');

        $this->authorizationService->assignOwnership($product, null);
    }

    public function test_assign_ownership_throws_when_no_user_logged_in(): void
    {
        $companyId = Uuid::v4();

        $this->userGranted
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $this->companyUserService
            ->expects($this->never())
            ->method('userBelongsToCompany');

        $product
            ->expects($this->never())
            ->method('setUserId');

        $product
            ->expects($this->never())
            ->method('setCompanyId');

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('You must be logged in to create a product');

        $this->authorizationService->assignOwnership($product, $companyId);
    }
}
