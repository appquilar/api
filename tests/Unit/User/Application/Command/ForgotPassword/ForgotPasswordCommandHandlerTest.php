<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Command\ForgotPassword;

use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Tests\Factories\User\Domain\Entity\UserFactory;
use App\Tests\Unit\UnitTestCase;
use App\User\Application\Command\ForgotPassword\ForgotPasswordCommand;
use App\User\Application\Command\ForgotPassword\ForgotPasswordCommandHandler;
use App\User\Application\Event\ForgotPasswordTokenCreated;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\ForgotPasswordTokenServiceInterface;
use App\User\Domain\Entity\User;
use App\User\Infrastructure\Entity\ForgotPasswordToken\ForgotPasswordToken;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

class ForgotPasswordCommandHandlerTest extends UnitTestCase
{
    private UserRepositoryInterface $userRepository;
    private ForgotPasswordTokenServiceInterface $forgotPasswordTokenService;
    private EventDispatcherInterface $eventDispatcher;
    private ForgotPasswordCommandHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->forgotPasswordTokenService = $this->createMock(ForgotPasswordTokenServiceInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->handler = new ForgotPasswordCommandHandler(
            $this->userRepository,
            $this->forgotPasswordTokenService,
            $this->eventDispatcher
        );
    }

    public function testHandleForgotPasswordCommandSuccessfully(): void
    {
        $email = 'user@example.com';
        $userId = Uuid::v4();
        $token = 'fake-token';
        /** @var User $user */
        $user = UserFactory::createOne(['userId' => $userId, 'email' => $email]);
        $forgotPasswordToken = new ForgotPasswordToken($user->getId(), Uuid::v4(), $token, new \DateTimeImmutable('+ 1 day'));

        $this->userRepository->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn($user);

        $this->forgotPasswordTokenService->expects($this->once())
            ->method('generateToken')
            ->with($userId)
            ->willReturn($forgotPasswordToken);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (ForgotPasswordTokenCreated $event) use ($email) {
                return $event->getEmail() === $email && $event->getToken() === 'fake-token';
            }));

        $command = new ForgotPasswordCommand($email);
        $this->handler->__invoke($command);
    }

    public function testHandleForgotPasswordCommandFailsWhenUserNotFound(): void
    {
        $email = 'nonexistent@example.com';

        $this->userRepository->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn(null);

        $this->forgotPasswordTokenService->expects($this->never())->method('generateToken');
        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('User not found');

        $command = new ForgotPasswordCommand($email);
        $this->handler->__invoke($command);
    }
}