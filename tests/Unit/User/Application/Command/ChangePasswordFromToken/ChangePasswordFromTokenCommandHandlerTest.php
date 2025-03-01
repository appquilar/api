<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Command\ChangePasswordFromToken;

use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Factories\User\Domain\Entity\UserFactory;
use App\Tests\Factories\User\Infrastructure\Entity\ForgotPasswordToken\ForgotPasswordTokenFactory;
use App\Tests\Unit\UnitTestCase;
use App\User\Application\Command\ChangePasswordFromToken\ChangePasswordFromTokenCommand;
use App\User\Application\Command\ChangePasswordFromToken\ChangePasswordFromTokenCommandHandler;
use App\User\Application\Repository\ForgotPasswordTokenRepositoryInterface;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\UserPasswordHasher;
use App\User\Domain\Entity\User;
use App\User\Infrastructure\Entity\ForgotPasswordToken\ForgotPasswordToken;
use Symfony\Component\Uid\Uuid;

class ChangePasswordFromTokenCommandHandlerTest extends UnitTestCase
{
    private UserRepositoryInterface $userRepositoryMock;
    private ForgotPasswordTokenRepositoryInterface $forgotPasswordTokenRepositoryMock;
    private UserPasswordHasher $passwordHasherMock;
    private ChangePasswordFromTokenCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->forgotPasswordTokenRepositoryMock = $this->createMock(ForgotPasswordTokenRepositoryInterface::class);
        $this->passwordHasherMock = $this->createMock(UserPasswordHasher::class);
        $this->handler = new ChangePasswordFromTokenCommandHandler(
            $this->userRepositoryMock,
            $this->forgotPasswordTokenRepositoryMock,
            $this->passwordHasherMock
        );
    }

    public function testSuccessfulPasswordChange(): void
    {
        $userId = Uuid::v4();
        $email = 'testuser@example.com';
        $token = 'valid-token';
        $newPassword = 'NewSecurePass123';
        $hashedPassword = 'hashedPassword123';
        /** @var User $user */
        $user = UserFactory::createOne(['userId' => $userId, 'email' => $email]);
        /** @var ForgotPasswordToken $forgotPasswordToken */
        $forgotPasswordToken = ForgotPasswordTokenFactory::createOne(
            ['userId' => $user->getId(), 'token' => $token, 'expiresAt' => new \DateTimeImmutable('+1 day')]
        );

        $this->userRepositoryMock->expects($this->once())
            ->method('findByEmail')
            ->with($user->getEmail())
            ->willReturn($user);

        $this->forgotPasswordTokenRepositoryMock->expects($this->once())
            ->method('getToken')
            ->with($token)
            ->willReturn($forgotPasswordToken);

        $this->passwordHasherMock->expects($this->once())
            ->method('hashPassword')
            ->with($newPassword)
            ->willReturn($hashedPassword);

        $user->setPassword($hashedPassword);

        $this->userRepositoryMock->expects($this->once())
            ->method('save')
            ->with($user);

        $this->forgotPasswordTokenRepositoryMock->expects($this->once())
            ->method('deleteToken')
            ->with($forgotPasswordToken);

        $command = new ChangePasswordFromTokenCommand($email, $token, $newPassword);
        $this->handler->__invoke($command);
    }

    public function testThrowsUnauthorizedExceptionIfUserNotFound(): void
    {
        $email = 'testuser@example.com';
        $token = 'valid-token';
        $newPassword = 'NewSecurePass123';
        $this->userRepositoryMock->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn(null);

        $this->expectException(UnauthorizedException::class);
        $this->handler->__invoke(
            new ChangePasswordFromTokenCommand($email, $token, $newPassword)
        );
    }

    public function testThrowsUnauthorizedExceptionIfTokenIsInvalid(): void
    {
        $userId = Uuid::v4();
        $email = 'testuser@example.com';
        $token = 'valid-token';
        $newPassword = 'NewSecurePass123';
        /** @var User $user */
        $user = UserFactory::createOne(['userId' => $userId, 'email' => $email]);

        $this->userRepositoryMock->expects($this->once())
            ->method('findByEmail')
            ->with($user->getEmail())
            ->willReturn($user);

        $this->forgotPasswordTokenRepositoryMock->expects($this->once())
            ->method('getToken')
            ->with($token)
            ->willReturn(null);

        $tokenRepository = $this->createMock(ForgotPasswordTokenRepositoryInterface::class);
        $tokenRepository->method('getToken')->willReturn(null);

        $this->expectException(UnauthorizedException::class);
        $this->handler->__invoke(
            new ChangePasswordFromTokenCommand($email, $token, $newPassword));
    }

    public function testThrowsUnauthorizedExceptionIfTokenIsExpired(): void
    {
        $userId = Uuid::v4();
        $email = 'testuser@example.com';
        $token = 'valid-token';
        $newPassword = 'NewSecurePass123';
        /** @var User $user */
        $user = UserFactory::createOne(['userId' => $userId, 'email' => $email]);
        /** @var ForgotPasswordToken $forgotPasswordToken */
        $forgotPasswordToken = ForgotPasswordTokenFactory::createOne(
            ['userId' => $user->getId(), 'token' => $token, 'expiresAt' => new \DateTimeImmutable('-1 day')]
        );

        $this->userRepositoryMock->expects($this->once())
            ->method('findByEmail')
            ->with($user->getEmail())
            ->willReturn($user);

        $this->forgotPasswordTokenRepositoryMock->expects($this->once())
            ->method('getToken')
            ->with($token)
            ->willReturn($forgotPasswordToken);

        $this->expectException(UnauthorizedException::class);
        $command = new ChangePasswordFromTokenCommand($email, $token, $newPassword);
        $this->handler->__invoke($command);
    }
}
