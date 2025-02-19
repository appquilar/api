<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Command\RegisterUser;

use App\User\Application\Command\RegisterUser\RegisterUserCommand;
use App\User\Application\Command\RegisterUser\RegisterUserCommandHandler;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class RegisterUserCommandHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $passwordHasher->method('hashPassword')->willReturn('hashed_password');

        $handler = new RegisterUserCommandHandler($userRepository, $passwordHasher);
        $command = new RegisterUserCommand(Uuid::v4(), 'test@example.com', 'SecurePass123');

        $userRepository->expects($this->once())->method('save')->with($this->isInstanceOf(User::class));

        $handler->__invoke($command);
    }
}
