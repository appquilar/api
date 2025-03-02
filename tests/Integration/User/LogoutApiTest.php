<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class LogoutApiTest extends IntegrationTestCase
{
    public function testLogoutSuccess(): void
    {
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());

        $response = $this->request('POST', '/api/auth/logout');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode(['success' => true]), $response->getContent());
    }

    public function testLogoutFailsWithInvalidToken(): void
    {
        $this->customHeaders['HTTP_AUTHORIZATION'] = 'Bearer invalid-token';
        $response = $this->request('POST', '/api/auth/logout');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertStringContainsString('Invalid access token', $response->getContent());
    }

    public function testLogoutFailsWithoutAuthentication(): void
    {
        $response = $this->request('POST', '/api/auth/logout');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertStringContainsString('Nonexistent token', $response->getContent());
    }

    public function testLogoutTwoTimesWillFail(): void
    {
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());

        $response = $this->request('POST', '/api/auth/logout');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode(['success' => true]), $response->getContent());

        $response = $this->request('POST', '/api/auth/logout');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertStringContainsString('Token is revoked or expired', $response->getContent());
    }
}
