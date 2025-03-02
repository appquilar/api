<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Service;

use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\User\Application\Dto\TokenPayload;
use App\User\Application\Service\AuthTokenServiceInterface;
use App\User\Infrastructure\Persistence\AccessTokenRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\Uid\Uuid;

class FirebaseJwtAuthTokenService implements AuthTokenServiceInterface
{
    public function __construct(
        private AccessTokenRepository $accessTokenRepository,
        private string $secret,
    ) {
    }

    public function encode(TokenPayload $tokenPayload): string
    {
        $payload = [
            'user_id' => $tokenPayload->getUserId()->toString(),
            'site_id' => Uuid::v4(),
            'exp' => $tokenPayload->getExpirationTime()
        ];

        $token = JWT::encode($payload, $this->secret, 'HS256');

        $this->accessTokenRepository->storeToken($tokenPayload, $payload['site_id'], $token);

        return $token;
    }

    public function decode(string $token): TokenPayload
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (\Throwable $e) {
            throw new UnauthorizedException('Invalid access token');
        }

        $accessToken = $this->accessTokenRepository->getToken($token);
        if ($accessToken === null) {
            throw new UnauthorizedException('Nonexistent access token');
        }

        return new TokenPayload(
            Uuid::fromString($decoded->user_id),
            $decoded->exp,
            $accessToken->isRevoked()
        );
    }

    public function revoke(string $token): void
    {
        $this->accessTokenRepository->revokeToken($token);
    }

    public function revokeTokensByUserId(Uuid $userId): void
    {
        $this->accessTokenRepository->revokeTokensByUserId($userId);
    }
}
