<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Command\ChangePassword;

use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\User\Domain\Entity\UserFactory;
use App\Tests\Unit\UnitTestCase;
use App\User\Application\Command\ChangePassword\ChangePasswordCommand;
use App\User\Application\Command\ChangePassword\ChangePasswordCommandHandler;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\UserPasswordHasher;
use App\User\Domain\Entity\User;

class ChangePasswordCommandHandlerTest extends UnitTestCase
{
    private UserRepositoryInterface $userRepository;
    private UserPasswordHasher $passwordHasher;
    private UserGranted $userGranted;
    private ChangePasswordCommandHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasher::class);
        $this->userGranted = $this->createMock(UserGranted::class);

        $this->handler = new ChangePasswordCommandHandler(
            $this->userRepository,
            $this->passwordHasher,
            $this->userGranted
        );
    }

    public function testSuccessfullyChangesPassword(): void
    {
        $oldPassword = 'old_password';
        $newPassword = 'new_password';
        $newHashedPassword = 'new_hashed_password';
        /** @var User $user */
        $user = UserFactory::createOne(['password' => $oldPassword]);

        $this->userGranted
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        $this->passwordHasher
            ->expects($this->once())
            ->method('verifyPassword')
            ->with($oldPassword, $user->getPassword())
            ->willReturn(true);
        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($newPassword)
            ->willReturn($newHashedPassword);

        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($user);
        $this->userGranted->expects($this->once())
            ->method('setUser')
            ->with($user);

        $command = new ChangePasswordCommand($newPassword, $oldPassword);
        $this->handler->__invoke($command);
    }

    public function testFailsWhenOldPasswordIsIncorrect(): void
    {
        $oldPassword = 'WrongPassword';
        $newPassword = 'NewSecurePassword456';
        /** @var User $user */
        $user = UserFactory::createOne(['password' => $oldPassword]);

        $this->userGranted->method('getUser')->willReturn($user);
        $this->passwordHasher->method('verifyPassword')->willReturn(false);

        $this->expectException(UnauthorizedException::class);

        $command = new ChangePasswordCommand($oldPassword, $newPassword);
        $this->handler->__invoke($command);
    }
}
