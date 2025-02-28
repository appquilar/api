<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\Integration\Context\UserContext;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class IntegrationTestCase extends WebTestCase
{
    use Factories,ResetDatabase;

    use UserContext;

    protected KernelBrowser $client;
    protected array $customHeaders = [];

    private const REQUEST_HEADERS = ['CONTENT_TYPE' => 'application/json'];

    protected function setUp(): void
    {
        parent::setUp();

        if (!static::$booted) {
            $this->client = static::createClient();
        }
    }

    private function getHeaders(): array
    {
        if ($this->accessToken === null) {
            return array_merge(
                self::REQUEST_HEADERS,
                $this->customHeaders
            );
        }

        return array_merge(
            self::REQUEST_HEADERS,
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->accessToken],
            $this->customHeaders
        );
    }

    protected function request(string $method, string $uri, array $payload = null): object
    {
        $this->client->request(
            method: $method,
            uri: $uri,
            server: $this->getHeaders(),
            content: $payload !== null ? json_encode($payload) : null
        );

        return $this->client->getResponse();
    }
}
