<?php

declare(strict_types=1);

namespace App\Notification\Application\Service;

interface EmailServiceInterface
{
    public function sendForgotPasswordEmail(string $email, string $name, string $token): void;
}
