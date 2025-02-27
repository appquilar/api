<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use App\User\Infrastructure\Request\Constraint\EmailIsUnique;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: "auth.register.user_id.not_blank")]
        #[Assert\Uuid(message: "auth.register.user_id.uuid")]
        public ?Uuid $userId = null,

        #[Assert\NotBlank(message: "auth.register.email.not_blank")]
        #[Assert\Email(message: "auth.register.email.email")]
        #[EmailIsUnique(message: "auth.register.email.unique")]
        public ?string $email = '',

        #[Assert\NotBlank(message: "auth.register.password.not_blank")]
        #[Assert\Length(min: 6, minMessage: "auth.register.password.length.min")]
        public ?string $password= ''
    ) {
    }
}
