<?php

declare(strict_types=1);

namespace App\User\Application\Command\ForgotPassword;

use App\Shared\Application\Command\CommandInterface;

class ForgotPasswordCommand implements CommandInterface
{
    public function __construct(
        private string $email
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
