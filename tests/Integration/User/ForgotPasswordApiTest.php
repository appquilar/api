<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordApiTest extends IntegrationTestCase
{
    public function testForgotPasswordSuccessWithExistentClient(): void
    {
        $this->givenAnUserWithEmail('user@example.com');
        $payload = [
            'email' => 'user@example.com'
        ];
        $response = $this->request('POST', '/api/auth/forgot-password', $payload);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testForgotPasswordSuccessWithNonexistentClient(): void
    {
        $payload = [
            'email' => 'user@example.com'
        ];
        $response = $this->request('POST', '/api/auth/forgot-password', $payload);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testForgotPasswordFailsWithInvalidEmail(): void
    {
        $payload = [
            'email' => 'invalid-email'
        ];
        $response = $this->request('POST', '/api/auth/forgot-password', $payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey('errors', $content['error']);
        $this->assertArrayHasKey('email', $content['error']['errors']);
        $this->assertEquals('auth.forgot_password.email.email', $content['error']['errors']['email'][0]);
    }

    public function testForgotPasswordFailsWithEmptyEmail(): void
    {
        $payload = [
            'email' => ''
        ];
        $response = $this->request('POST', '/api/auth/forgot-password', $payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey('errors', $content['error']);
        $this->assertArrayHasKey('email', $content['error']['errors']);
        $this->assertEquals('auth.forgot_password.email.not_blank', $content['error']['errors']['email'][0]);
    }
}
