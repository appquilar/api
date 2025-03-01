<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: "auth.forgot_password.email.not_blank")]
        #[Assert\Email(message: "auth.forgot_password.email.email")]
        public ?string $email = '',
    ) {
    }
}
