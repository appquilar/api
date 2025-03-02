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

        $response = $this->request('POST', '/api/auth/login', [
            'email' => $email,
            'password' => $password
        ]);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('token', $responseData['data']);
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        $email = 'wpuser@example.com';
        $password = 'WordpressPassword123';
        $this->givenAnUserWithEmailAndPassword($email, $password);

        $response = $this->request('POST', '/api/auth/login',[
            'email' => 'wpuser@example.com',
            'password' => 'WrongPassword'
        ]);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
