<?php

declare(strict_types=1);

namespace App\User\Application\Command\ChangePasswordFromToken;

use App\Shared\Application\Command\Command;

class ChangePasswordFromTokenCommand implements Command
{
    public function __construct(
        private string $email,
        private string $token,
        private string $password,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
