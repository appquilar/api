<?php

declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Tests\Factories\User\Domain\Entity\UserFactory;
use function Zenstruck\Foundry\faker;

trait UserContext
{
    protected ?string $accessToken = null;

    protected function givenAnUserWithEmail(string $email): void
    {
        UserFactory::createOne(['email' => $email]);
    }

    protected function givenAnUserWithEmailAndPassword(string $email, string $password): void
    {
        UserFactory::createOne(['email' => $email, 'password' => $password]);
    }

    protected function givenAnUserWithEmailAndWordpressPassword(string $email, string $password): void
    {
        UserFactory::createOne(['email' => $email, 'password' => faker()->password(), 'wordpress_password' => $password]);
    }

    protected function givenImLoggedInAsRegularUserWithEmail(string $email): void
    {
        $password = 'test123';
        $this->givenAnUserWithEmailAndPassword($email, $password);
        $payload = ['email' => $email, 'password' => $password];

        $response = $this->request('POST', '/api/auth/login', $payload);
        $this->accessToken = json_decode($response->getContent(), true)['data']['token'];
    }
}
