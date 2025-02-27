<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\Factories\User\Domain\Entity\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use function Zenstruck\Foundry\faker;

class IntegrationTestCase extends WebTestCase
{
    use Factories,ResetDatabase;

    protected KernelBrowser $client;

    private const REQUEST_HEADERS = ['CONTENT_TYPE' => 'application/json'];

    protected function setUp(): void
    {
        parent::setUp();

        if (!static::$booted) {
            $this->client = static::createClient();
        }
    }

    protected function request(string $method, string $uri, array $payload = null): object
    {
        $this->client->request(
            method: $method,
            uri: $uri,
            server: self::REQUEST_HEADERS,
            content: $payload !== null ? json_encode($payload) : null
        );

        return $this->client->getResponse();
    }

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
}
