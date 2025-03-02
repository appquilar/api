<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetUserByIdDto implements RequestDtoInterface
{

    public function __construct(
        #[Assert\NotBlank(message: "user.get_by_id.user_id.not_blank")]
        #[Assert\Uuid(message: "user.get_by_id.user_id.uuid")]
        public ?Uuid $userId = null
    ) {
    }
}
