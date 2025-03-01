<?php

declare(strict_types=1);

namespace App\User\Application\Event;

use Symfony\Component\Uid\Uuid;

class ForgotPasswordTokenCreated
{
    public function __construct(
        private string $name,
        private string $email,
        private string $token,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
