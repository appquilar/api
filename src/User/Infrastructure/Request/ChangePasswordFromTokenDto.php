<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordFromTokenDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: "auth.change_password.email.not_blank")]
        #[Assert\Email(message: "auth.change_password.email.email")]
        public ?string $email = '',

        #[Assert\NotBlank(message: "auth.change_password.email.token")]
        public ?string $token = '',

        #[Assert\NotBlank(message: "auth.change_password.password.not_blank")]
        #[Assert\Length(min: 6, minMessage: "auth.change_password.password.length.min")]
        public ?string $password = '',
    ) {
    }
}
