<?php

declare(strict_types=1);

namespace App\User\Application\Query\Login;

use App\Shared\Application\Query\QueryResult;

class LoginQueryResult implements QueryResult
{
    public function __construct(
        private string $token
    ) {
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
