<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginApiTest extends IntegrationTestCase
{
    public function testLoginWithSymfonyUser(): void
    {
        $email = 'symfonyuser@example.com';
        $password = 'SymfonyPassword123';
        $payload = ['email' => $email, 'password' => $password];
        $this->givenAnUserWithEmailAndPassword($email, $password);

        $response = $this->request('POST', '/api/auth/login', $payload);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('token', $responseData['data']);
    }

    public function testLoginWithWordPressUser(): void
    {
        $email = 'wpuser@example.com';
        $password = 'WordpressPassword123';
        $this->givenAnUserWithEmailAndWordpressPassword($email, $password);
        $this->client->request('POST', '/api/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $email,
            'password' => $password
        ]));

        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('token', $responseData['data']);
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        $this->client->request('POST', '/api/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'wpuser@example.com',
            'password' => 'WrongPassword'
        ]));

        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
