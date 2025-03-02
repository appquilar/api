<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistence;

use App\User\Application\Dto\TokenPayload;
use App\User\Infrastructure\Entity\AccessToken\AccessToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class AccessTokenRepository
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function getToken(string $token): ?AccessToken
    {
        return $this->entityManager->getRepository(AccessToken::class)->findOneBy(['token' => $token]);
    }

    public function storeToken(TokenPayload $tokenPayload, Uuid $siteId, string $token): void
    {
        $accessToken = new AccessToken($tokenPayload->getUserId(), $siteId, $token);
        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();
    }

    public function revokeToken(string $token): void
    {
        $accessToken = $this->entityManager->getRepository(AccessToken::class)
            ->findOneBy(['token' => $token]);

        if ($accessToken) {
            $accessToken->setRevoked(true);
            $this->entityManager->flush();
        }
    }

    public function revokeTokensByUserId(Uuid $userId): void
    {
        $this->entityManager->createQuery(
            'UPDATE ' . AccessToken::class . ' t 
             SET t.revoked = true 
             WHERE t.userId = :userId'
        )->setParameter('userId', $userId->toBinary())->execute();
    }
}
