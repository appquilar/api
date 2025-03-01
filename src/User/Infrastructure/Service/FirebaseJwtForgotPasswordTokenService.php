<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Service;

use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\User\Application\Service\ForgotPasswordTokenServiceInterface;
use App\User\Infrastructure\Entity\ForgotPasswordToken\ForgotPasswordToken;
use App\User\Infrastructure\Persistence\ForgotPasswordTokenRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\Uid\Uuid;

class FirebaseJwtForgotPasswordTokenService implements ForgotPasswordTokenServiceInterface
{
    private const TOKEN_TTL = 60*60*3; //3 hours
    public function __construct(
        private ForgotPasswordTokenRepository $forgotPasswordTokenRepository,
        private string $secret
    ) {
    }

    public function generateToken(Uuid $userId): ForgotPasswordToken
    {
        $payload = [
            'user_id' => $userId,
            'site_id' => Uuid::v4(),
            'exp' => time() + self::TOKEN_TTL
        ];

        $token = JWT::encode($payload, $this->secret, 'HS256');

        $forgotPasswordToken = new ForgotPasswordToken(
            $userId,
            $payload['site_id'],
            $token,
            \DateTimeImmutable::createFromFormat('U', (string) $payload['exp'])
        );
        $this->forgotPasswordTokenRepository->storeToken($forgotPasswordToken);

        return $forgotPasswordToken;
    }

    public function decodeToken(string $token): ForgotPasswordToken
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (\Throwable $e) {
            throw new UnauthorizedException('Token can\'t be decoded');
        }

        $forgotPasswordToken = $this->forgotPasswordTokenRepository->getToken($token);
        if ($forgotPasswordToken === null) {
            throw new UnauthorizedException('Nonexistent forgot password token');
        }

        return $forgotPasswordToken;
    }
}
