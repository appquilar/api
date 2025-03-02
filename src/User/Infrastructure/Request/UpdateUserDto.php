<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use App\Shared\Infrastructure\Security\UserRole;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserDto implements RequestDtoInterface
{
    /**
     * @param UserRole[]|null $roles
     */
    public function __construct(
        #[Assert\NotBlank(message: "user.update.user_id.not_blank")]
        #[Assert\Uuid(message: "user.update.user_id.uuid")]
        public ?Uuid $userId = null,

        #[Assert\NotBlank(message: "user.update.first_name.not_blank")]
        public ?string $firstName = null,

        #[Assert\NotBlank(message: "user.update.last_name.not_blank")]
        public ?string $lastName = null,

        #[Assert\NotBlank(message: "user.update.email.not_blank")]
        #[Assert\Email(message: "user.update.email.email")]
        public ?string $email = null,

        public ?array $roles = []
    ) {
    }
}
