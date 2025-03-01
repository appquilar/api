<?php

declare(strict_types=1);

namespace App\User\Application\Repository;

use App\User\Infrastructure\Entity\ForgotPasswordToken\ForgotPasswordToken;

interface ForgotPasswordTokenRepositoryInterface
{
    public function getToken(string $token): ?ForgotPasswordToken;
    public function deleteToken(ForgotPasswordToken $forgotPasswordToken): void;
}
