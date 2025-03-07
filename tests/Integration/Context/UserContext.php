<?php

declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Shared\Infrastructure\Security\UserRole;
use App\Tests\Factories\User\Domain\Entity\PersistingUserFactory;
use App\Tests\Factories\User\Infrastructure\Entity\AccessToken\PersistingAccessTokenFactory;
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

    protected function givenARegularUserWithUserId(Uuid $userId): void
    {
        PersistingUserFactory::createOne(['userId' => $userId, 'roles' => [UserRole::REGULAR_USER]]);
    }

    protected function givenARegularUserWithUserIdAndPassword(Uuid $userId, string $password): void
    {
        PersistingUserFactory::createOne(['userId' => $userId, 'password' => $password, 'roles' => [UserRole::REGULAR_USER]]);
    }

    protected function givenAnAdminUserWithUserId(Uuid $userId): void
    {
        PersistingUserFactory::createOne(['userId' => $userId, 'roles' => [UserRole::ADMIN]]);
    }

    protected function givenAForgotPasswordTokenWithUserIdAndTokenAndExpirationDate(
        Uuid $userId,
        string $token,
        \DateTimeImmutable $expiresAt
    ): void
    {
        PersistingForgotPasswordTokenFactory::createOne(['userId' => $userId, 'token' => $token, 'expiresAt' => $expiresAt]);
    }

    protected function givenImLoggedInAsRegularUserWithUserId(Uuid $userId): void
    {
        $this->givenARegularUserWithUserId($userId);

        $this->login($userId);
    }

    protected function givenImLoggedInAsRegularUserWithUserIdAndPassword(Uuid $userId, string $password): void
    {
        $this->givenARegularUserWithUserIdAndPassword($userId, $password);

        $this->login($userId);
    }

    protected function givenImLoggedInAsAdmin(): void
    {
        $userId = Uuid::v4();
        $this->givenAnAdminUserWithUserId($userId);

        $this->login($userId);
    }

    private function login(Uuid $userId): void
    {
        $accessToken = PersistingAccessTokenFactory::createOne(['userId' => $userId]);
        $this->accessToken = $accessToken->getToken();
    }
}
