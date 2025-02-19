<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Command\RegisterUser;


use App\User\Application\Command\RegisterUser\RegisterUserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class RegisterUserCommandTest extends TestCase
{
    public function testRegisterUserCommandStoresData(): void
    {
        $command = new RegisterUserCommand(Uuid::v4(), 'test@example.com', 'SecurePass123');

        $this->assertEquals('test@example.com', $command->email);
        $this->assertEquals('SecurePass123', $command->password);
    }
}
