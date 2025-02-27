<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Service;

use App\User\Application\Dto\TokenPayload;
use App\User\Application\Service\AuthTokenServiceInterface;
use Firebase\JWT\JWT;

class FirebaseJwtAuthTokenService implements AuthTokenServiceInterface
{
    public function encode(TokenPayload $payload): string
    {
        $payload = [
            'user_id' => $payload->getUserId()->toString(),
            'email' => $payload->getEmail(),
            'site_id' => 'xxx',
            'exp' => $payload->getExpirationTime()
        ];

        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }

    public function decode(string $token): TokenPayload
    {
        // TODO: Implement decode() method.
    }
}
