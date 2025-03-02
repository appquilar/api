<?php

declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Shared\Infrastructure\Security\UserRole;
use App\Tests\Factories\User\Domain\Entity\PersistingUserFactory;
use App\Tests\Factories\User\Infrastructure\Entity\ForgotPasswordToken\PersistingForgotPasswordTokenFactory;
use Symfony\Component\Uid\Uuid;
use function Zenstruck\Foundry\faker;

trait UserContext
{
    protected ?string $accessToken = null;

    protected function givenAnUserWithIdAndEmail(Uuid $userId, string $email): void
    {
        PersistingUserFactory::createOne(['userId' => $userId, 'email' => $email]);
    }

    protected function givenAnUserWithEmail(string $email): void
    {
        PersistingUserFactory::createOne(['email' => $email]);
    }

    protected function givenAnUserWithEmailAndPassword(string $email, string $password): void
    {
        PersistingUserFactory::createOne(['email' => $email, 'password' => $password]);
    }

    protected function givenAnUserWithEmailAndWordpressPassword(string $email, string $password): void
    {
        PersistingUserFactory::createOne(['email' => $email, 'password' => faker()->password(), 'wordpress_password' => $password]);
    }

    protected function givenAnAdminUserWithEmailAndPassword(string $email, string $password): void
    {
        PersistingUserFactory::createOne(['email' => $email, 'password' => $password, 'roles' => [UserRole::ADMIN]]);
    }

    protected function givenAForgotPasswordTokenWithUserIdAndTokenAndExpirationDate(
        Uuid $userId,
        string $token,
        \DateTimeImmutable $expiresAt
    ): void
    {
        PersistingForgotPasswordTokenFactory::createOne(['userId' => $userId, 'token' => $token, 'expiresAt' => $expiresAt]);
    }

    protected function givenImLoggedInAsRegularUserWithEmail(string $email): void
    {
        $password = 'test123';
        $this->givenAnUserWithEmailAndPassword($email, $password);
        $payload = ['email' => $email, 'password' => $password];

        $this->login($payload);
    }

    protected function givenImLoggedInAsAdminUserWithEmail(string $email): void
    {
        $password = 'test123';
        $this->givenAnAdminUserWithEmailAndPassword($email, $password);
        $payload = ['email' => $email, 'password' => $password];

        $this->login($payload);
    }

    private function login(array $payload): void
    {
        $response = $this->request('POST', '/api/auth/login', $payload);
        $this->accessToken = json_decode($response->getContent(), true)['data']['token'];
    }
}
