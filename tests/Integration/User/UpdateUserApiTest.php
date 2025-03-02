<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Shared\Infrastructure\Security\UserRole;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class UpdateUserApiTest extends IntegrationTestCase
{
    public function testUpdateUserSuccessOwnUserWithoutRoles(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $response = $this->request('PATCH', "/api/users/" . $userId->toString(), [
            'first_name' => 'Updated',
            'last_name' => 'User',
            'email' => 'test@example.com'
        ]);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testUpdateUserRolesRevokesTokens(): void
    {
        $userId = Uuid::v4();
        $email = 'test@example.com';
        $this->givenAnUserWithIdAndEmail($userId, $email);
        $this->givenImLoggedInAsAdmin();

        $this->request('PATCH', "/api/users/" . $userId->toString(), [
            'first_name' => 'Updated',
            'last_name' => 'User',
            'email' => $email,
            'roles' => [UserRole::REGULAR_USER, UserRole::ADMIN]
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testUnauthorizedUserCannotUpdateAnotherUser(): void
    {
        $authorizedUserId = Uuid::v4();
        $userId = Uuid::v4();
        $email = 'test@example.com';
        $this->givenImLoggedInAsRegularUserWithUserId($authorizedUserId);
        $this->givenAnUserWithIdAndEmail($userId, $email);

        $this->request('PATCH', "/api/users/" . $userId->toString(), [
            'first_name' => 'Updated',
            'last_name' => 'User',
            'email' => $email,
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testUserNotFound(): void
    {
        $authorizedUserId = Uuid::v4();
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($authorizedUserId);

        $this->request('PATCH', "/api/users/" . $userId->toString(), [
            'first_name' => 'Updated',
            'last_name' => 'User',
            'email' => 'test@example.com'
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testTryToUpdateEmailWithAlreadyExistentEmail(): void
    {
        $userId = Uuid::v4();
        $alreadyExistentEmail = 'exists@example.com';
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenAnUserWithEmail($alreadyExistentEmail);

        $response = $this->request('PATCH', "/api/users/" . $userId->toString(), [
            'first_name' => 'Updated',
            'last_name' => 'User',
            'email' => $alreadyExistentEmail
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
