<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LoginUserDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: "auth.login.email.not_blank")]
        #[Assert\Email(message: "auth.login.email.email")]
        public ?string $email = '',

        #[Assert\NotBlank(message: "auth.login.password.not_blank")]
        public ?string $password= ''
    ) {
    }

}
