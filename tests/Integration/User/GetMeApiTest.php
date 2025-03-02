<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetMeApiTest extends IntegrationTestCase
{
    public function testGetMeReturnsUserDataForRegularUser(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $response = $this->request('GET', '/api/me');
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('user_id', $responseData['data']);
        $this->assertEquals($userId->toString(), $responseData['data']['user_id']);
        $this->assertArrayHasKey('first_name', $responseData['data']);
        $this->assertArrayHasKey('last_name', $responseData['data']);
        $this->assertArrayHasKey('email', $responseData['data']);
    }

    public function testGetMeReturnsAdminDataForAdminUser(): void
    {
        $this->givenImLoggedInAsAdmin();

        $response = $this->request('GET', '/api/me');
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('roles', $responseData['data']);
    }

    public function testGetMeUnauthorizedWithoutToken(): void
    {
        $this->request('GET', '/api/me');

        $response = $this->request('GET', '/api/me');
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertArrayHasKey('error', $responseData);
    }
}
