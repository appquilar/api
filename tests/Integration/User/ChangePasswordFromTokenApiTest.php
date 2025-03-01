<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Factories\User\Domain\Entity\PersistingUserFactory;
use App\Tests\Factories\User\Infrastructure\Entity\ForgotPasswordToken\PersistingForgotPasswordTokenFactory;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ChangePasswordFromTokenApiTest extends IntegrationTestCase
{
    public function testChangePasswordSuccess(): void
    {
        $userId = Uuid::v4();
        $email = 'testuser@example.com';
        $token = 'valid-token';
        $newPassword = 'NewSecurePass123';
        $payload = [
            'email' => $email,
            'token' => $token,
            'password' => $newPassword
        ];

        $this->givenAnUserWithIdAndEmail($userId, $email);
        $this->givenAForgotPasswordTokenWithUserIdAndTokenAndExpirationDate($userId, $token, new \DateTimeImmutable('+1 day'));

        $response = $this->request('POST', '/api/auth/change-password', $payload);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testChangePasswordFailsForInvalidToken(): void
    {
        $userId = Uuid::v4();
        $email = 'testuser@example.com';
        $token = 'invalid-token';
        $newPassword = 'NewSecurePass123';
        $payload = [
            'email' => $email,
            'token' => $token,
            'password' => $newPassword
        ];

        $this->givenAnUserWithIdAndEmail($userId, $email);

        $response = $this->request('POST', '/api/auth/change-password', $payload);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Invalid token', $content['error'][0]);
    }

    public function testChangePasswordFailsForExpiredToken(): void
    {
        $userId = Uuid::v4();
        $email = 'testuser@example.com';
        $token = 'valid-token';
        $newPassword = 'NewSecurePass123';
        $payload = [
            'email' => $email,
            'token' => $token,
            'password' => $newPassword
        ];

        $this->givenAnUserWithIdAndEmail($userId, $email);
        $this->givenAForgotPasswordTokenWithUserIdAndTokenAndExpirationDate($userId, $token, new \DateTimeImmutable('-1 day'));

        $response = $this->request('POST', '/api/auth/change-password', $payload);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Invalid token', $content['error'][0]);
    }

    public function testChangePasswordFailsForNonExistentUser(): void
    {
        $userId = Uuid::v4();
        $email = 'testuser@example.com';
        $token = 'valid-token';
        $newPassword = 'NewSecurePass123';
        $payload = [
            'email' => 'invaliduser@example.com',
            'token' => $token,
            'password' => $newPassword
        ];

        $this->givenAnUserWithIdAndEmail($userId, $email);
        $this->givenAForgotPasswordTokenWithUserIdAndTokenAndExpirationDate($userId, $token, new \DateTimeImmutable('+1 day'));

        $response = $this->request('POST', '/api/auth/change-password', $payload);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('User not found', $content['error'][0]);
    }
}
