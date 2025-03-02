<?php

declare(strict_types=1);

namespace App\User\Application\Service;

use App\User\Infrastructure\Entity\ForgotPasswordToken\ForgotPasswordToken;
use Symfony\Component\Uid\Uuid;

interface ForgotPasswordTokenServiceInterface
{
    public function generateToken(Uuid $userId): ForgotPasswordToken;
}
