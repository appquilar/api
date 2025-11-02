<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\Integration\Context\CategoryContext;
use App\Tests\Integration\Context\CompanyContext;
use App\Tests\Integration\Context\CompanyUserContext;
use App\Tests\Integration\Context\ImageContext;
use App\Tests\Integration\Context\SiteContext;
use App\Tests\Integration\Context\UserContext;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class IntegrationTestCase extends WebTestCase
{
    use Factories,ResetDatabase;
    use UserContext,CompanyContext,CompanyUserContext,ImageContext,CategoryContext,SiteContext;

    private const REQUEST_HEADERS = ['CONTENT_TYPE' => 'application/json'];

    protected KernelBrowser $client;
    protected array $customHeaders = [];
    protected string $testRootPath;

    protected function setUp(): void
    {
        parent::setUp();

        static::ensureKernelShutdown();
        $this->client = static::createClient();

        $this->testRootPath = self::$kernel->getContainer()->getParameter('kernel.project_dir') . '/tests';
        $this->testStoragePath = self::$kernel->getContainer()->getParameter('kernel.project_dir') . '/var/uploads/test';

        if (!is_dir($this->testStoragePath)) {
            mkdir($this->testStoragePath, 0777, true);
        }
    }

    private function getHeaders(): array
    {
        if ($this->accessToken === null) {
            return array_merge(
                self::REQUEST_HEADERS,
                $this->customHeaders,
            );
        }

        return array_merge(
            self::REQUEST_HEADERS,
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->accessToken],
            $this->customHeaders,
        );
    }

    protected function request(string $method, string $uri, array $payload = null, array $files = []): object
    {
        $this->client->request(
            method: $method,
            uri: $uri,
            files: $files,
            server: $this->getHeaders(),
            content: $payload !== null ? json_encode($payload) : null
        );

        return $this->client->getResponse();
    }

    protected function assertErrorMessageExists(string $field, array $content): void
    {
        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey('errors', $content['error']);
        $this->assertArrayHasKey($field, $content['error']['errors']);
    }
}
