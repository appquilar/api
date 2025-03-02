<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: "user.change_password.old_password.not_blank")]
        public ?string $oldPassword = '',

        #[Assert\NotBlank(message: "user.change_password.new_password.not_blank")]
        #[Assert\Length(min: 6, minMessage: "user.change_password.new_password.length.min")]
        public ?string $newPassword = '',
    ) {
    }
}
