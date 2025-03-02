<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Command\UpdateUser;

use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\Unauthorized\NotEnoughPermissionsException;
use App\Shared\Infrastructure\Security\UserRole;
use App\Tests\Unit\UnitTestCase;
use App\User\Application\Command\UpdateUser\UpdateUserCommand;
use App\User\Application\Command\UpdateUser\UpdateUserCommandHandler;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\AuthTokenServiceInterface;
use App\User\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;

class UpdateUserCommandHandlerTest extends UnitTestCase
{
    private UserRepositoryInterface $userRepository;
    private UserGranted $userGranted;
    private AuthTokenServiceInterface $authTokenService;
    private UpdateUserCommandHandler $handler;
    private User $user;
    private User $adminUser;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->userGranted = $this->createMock(UserGranted::class);
        $this->authTokenService = $this->createMock(AuthTokenServiceInterface::class);

        // Mock users
        $this->user = new User(Uuid::v4(), 'user@example.com', 'Password123');
        $this->user->setRoles([UserRole::REGULAR_USER]);

        $this->adminUser = new User(Uuid::v4(), 'admin@example.com', 'AdminPass123');
        $this->adminUser->setRoles([UserRole::REGULAR_USER, UserRole::ADMIN]);

        $this->handler = new UpdateUserCommandHandler(
            $this->userRepository,
            $this->userGranted,
            $this->authTokenService
        );
    }

    public function testUserCanUpdateOwnInfo(): void
    {
        $this->userGranted->method('getUser')->willReturn($this->user);
        $this->userGranted->method('isAdmin')->willReturn(false);

        $command = new UpdateUserCommand(
            $this->user->getId(),
            'Updated',
            'User',
            'updated@example.com',
            []
        );

        $this->userRepository->method('findById')->willReturn($this->user);
        $this->userRepository->expects($this->once())->method('save')->with($this->user);

        $this->handler->__invoke($command);

        $this->assertEquals('updated@example.com', $this->user->getEmail());
        $this->assertEquals('Updated', $this->user->getFirstName());
        $this->assertEquals('User', $this->user->getLastName());
    }

    public function testAdminCanUpdateOtherUserInfo(): void
    {
        $this->userGranted->method('getUser')->willReturn($this->adminUser);
        $this->userGranted->method('isAdmin')->willReturn(true);

        $command = new UpdateUserCommand(
            $this->user->getId(),
            'Updated',
            'User',
            'updated@example.com',
            [UserRole::REGULAR_USER, UserRole::ADMIN]
        );

        $this->userRepository->method('findById')->willReturn($this->user);
        $this->userRepository->expects($this->once())->method('save')->with($this->user);

        $this->handler->__invoke($command);

        $this->assertEquals('updated@example.com', $this->user->getEmail());
        $this->assertEquals('Updated', $this->user->getFirstName());
        $this->assertEquals('User', $this->user->getLastName());
        $this->assertEquals([UserRole::REGULAR_USER, UserRole::ADMIN], $this->user->getRoles());
    }

    public function testRegularUserCannotUpdateAnotherUser(): void
    {
        $anotherUser = new User(Uuid::v4(), 'anotheruser@example.com', 'AnotherPass123');

        $this->userGranted->method('getUser')->willReturn($this->user);
        $this->userGranted->method('isAdmin')->willReturn(false);

        $command = new UpdateUserCommand(
            $anotherUser->getId(),
            'Hacker',
            'Attack',
            'hacker@example.com'
        );

        $this->userRepository->method('findById')->willReturn($anotherUser);

        $this->expectException(NotEnoughPermissionsException::class);
        $this->handler->__invoke($command);
    }

    public function testCannotUpdateToExistingEmail(): void
    {
        $existingUser = new User(Uuid::v4(), 'existing@example.com', 'ExistingPass123');

        $this->userGranted->method('getUser')->willReturn($this->user);
        $this->userGranted->method('isAdmin')->willReturn(false);

        $command = new UpdateUserCommand(
            $this->user->getId(),
            'Updated',
            'User',
            'existing@example.com'
        );

        $this->userRepository->method('findById')->willReturn($this->user);
        $this->userRepository->method('findByEmail')->willReturn($existingUser);

        $this->expectException(BadRequestException::class);
        $this->handler->__invoke($command);
    }

    public function testHandleThrowsBadRequestIfUserNotFound(): void
    {
        $command = new UpdateUserCommand(
            Uuid::v4(),
            'Test',
            'User',
            'test@example.com',
        );

        $this->userRepository->method('findById')->willReturn(null);

        $this->expectException(BadRequestException::class);
        $this->handler->__invoke($command);
    }
}
