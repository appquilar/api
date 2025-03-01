<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistence;

use App\User\Application\Repository\ForgotPasswordTokenRepositoryInterface;
use App\User\Infrastructure\Entity\ForgotPasswordToken\ForgotPasswordToken;
use Doctrine\ORM\EntityManagerInterface;

class ForgotPasswordTokenRepository implements ForgotPasswordTokenRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function getToken(string $token): ?ForgotPasswordToken
    {
        return $this->entityManager->getRepository(ForgotPasswordToken::class)->findOneBy(['token' => $token]);
    }

    public function storeToken(ForgotPasswordToken $forgotPasswordToken): void
    {
        $this->entityManager->persist($forgotPasswordToken);
        $this->entityManager->flush();
    }

    public function deleteToken(ForgotPasswordToken $forgotPasswordToken): void
    {
        $this->entityManager->remove($forgotPasswordToken);
        $this->entityManager->flush();
    }
}
