<?php

declare(strict_types=1);

namespace App\User\Application\Service;

use App\User\Application\Dto\TokenPayload;
use Symfony\Component\Uid\Uuid;

interface AuthTokenServiceInterface
{
    public function encode(TokenPayload $payload): string;
    public function decode(string $token): TokenPayload;
}
