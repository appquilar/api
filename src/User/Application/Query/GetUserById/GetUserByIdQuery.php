<?php

declare(strict_types=1);

namespace App\User\Application\Query\GetUserById;

use App\Shared\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

class GetUserByIdQuery implements Query
{
    public function __construct(
        private Uuid $userId
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
