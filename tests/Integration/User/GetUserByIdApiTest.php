<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetUserByIdApiTest extends IntegrationTestCase
{
    public function testGetUserByIdReturnsUserDataForRegularUserWithoutEmail(): void
    {
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());
        $expectedUserId = Uuid::v4();
        $expectedEmail = 'test@example.com';
        $this->givenAnUserWithIdAndEmail($expectedUserId, $expectedEmail);

        $response = $this->request('GET', '/api/users/' . $expectedUserId->toString());
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('user_id', $responseData['data']);
        $this->assertEquals($expectedUserId->toString(), $responseData['data']['user_id']);
        $this->assertArrayHasKey('first_name', $responseData['data']);
        $this->assertArrayHasKey('last_name', $responseData['data']);
        $this->assertArrayNotHasKey('email', $responseData['data']);
    }

    public function testGetMeReturnsAdminDataForAdminUser(): void
    {
        $this->givenImLoggedInAsAdmin();
        $expectedUserId = Uuid::v4();
        $expectedEmail = 'test@example.com';
        $this->givenAnUserWithIdAndEmail($expectedUserId, $expectedEmail);

        $response = $this->request('GET', '/api/users/' . $expectedUserId->toString());
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('user_id', $responseData['data']);
        $this->assertEquals($expectedUserId->toString(), $responseData['data']['user_id']);
        $this->assertArrayHasKey('first_name', $responseData['data']);
        $this->assertArrayHasKey('last_name', $responseData['data']);
        $this->assertArrayHasKey('email', $responseData['data']);
        $this->assertEquals($expectedEmail, $responseData['data']['email']);
        $this->assertArrayHasKey('roles', $responseData['data']);
    }
}
