<?php

declare(strict_types=1);

namespace App\Notification\Application\Service;

use Symfony\Component\Uid\Uuid;

interface EmailServiceInterface
{
    public function sendForgotPasswordEmail(string $email, string $name, string $token): void;
    public function sendCompanyUserInvitationEmail(Uuid $companyId, string $companyName, string $email, string $token): void;
}
