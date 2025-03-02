<?php

declare(strict_types=1);

namespace App\User\Application\Query\GetUserById;

use App\Shared\Application\Query\QueryResult;

class GetUserByIdQueryResult implements QueryResult
{
    public function __construct(
        private array $user
    ) {
    }

    public function getUser(): array
    {
        return $this->user;
    }
}
