<?php

declare(strict_types=1);

namespace App\User\Application\Service;

use App\User\Application\Dto\TokenPayload;

interface AuthTokenServiceInterface
{
    public function encode(TokenPayload $tokenPayload): string;
    public function decode(string $token): TokenPayload;
    public function revoke(string $token): void;
}
