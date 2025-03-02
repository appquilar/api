<?php

declare(strict_types=1);

namespace App\User\Application\Command\ChangePassword;

use App\Shared\Application\Command\Command;

class ChangePasswordCommand implements Command
{
    public function __construct(
        private string $newPassword,
        private string $oldPassword
    ) {
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }
}
