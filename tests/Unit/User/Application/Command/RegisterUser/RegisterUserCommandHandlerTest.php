<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Command\RegisterUser;

use App\Tests\Unit\UnitTestCase;
use App\User\Application\Command\RegisterUser\RegisterUserCommand;
use App\User\Application\Command\RegisterUser\RegisterUserCommandHandler;
use App\User\Application\Event\UserRegistered;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\UserPasswordHasher;
use App\User\Domain\Entity\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

class RegisterUserCommandHandlerTest extends UnitTestCase
{
    public function testHandle(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordHasher = $this->createMock(UserPasswordHasher::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $passwordHasher->method('hashPassword')->willReturn('hashed_password');

        $handler = new RegisterUserCommandHandler($userRepository, $passwordHasher, $eventDispatcher);
        $command = new RegisterUserCommand(Uuid::v4(), 'test@example.com', 'SecurePass123');

        $userRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with(new UserRegistered(
                $command->userId,
                $command->email,
            ));

        $handler->__invoke($command);
    }
}
