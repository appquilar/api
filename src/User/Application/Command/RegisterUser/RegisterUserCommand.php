<?php

declare(strict_types=1);

namespace App\User\Application\Command\RegisterUser;

use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class RegisterUserCommand implements Command
{
    public function __construct(public Uuid $userId, public string $email, public string $password)
    {
    }
}
