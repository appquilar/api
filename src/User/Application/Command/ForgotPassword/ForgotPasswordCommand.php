<?php

declare(strict_types=1);

namespace App\User\Application\Command\ForgotPassword;

use App\Shared\Application\Command\Command;

class ForgotPasswordCommand implements Command
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
