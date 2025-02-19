<?php

declare(strict_types=1);

namespace App\User\Application\Command\RegisterUser;

use App\Shared\Application\Command\CommandInterface;
use Symfony\Component\Uid\Uuid;

class RegisterUserCommand implements CommandInterface
{
    public function __construct(public Uuid $userId, public string $email, public string $password)
    {
    }
}
