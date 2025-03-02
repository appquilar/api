<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ChangePasswordApiTest extends IntegrationTestCase
{
    public function testUserCanChangePasswordSuccessfully(): void
    {
        $userId = Uuid::v4();
        $oldPassword = 'OldPassword123';
        $this->givenImLoggedInAsRegularUserWithUserIdAndPassword($userId, $oldPassword);

        $response = $this->request('PATCH', "/api/users/" . $userId->toString() . "/change-password", [
            'oldPassword' => $oldPassword,
            'newPassword' => 'NewSecurePassword456'
        ]);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testFailsWhenOldPasswordIsIncorrect(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $response = $this->request('PATCH', "/api/users/" . $userId->toString() . "/change-password", [
            'oldPassword' => 'WrongPassword',
            'newPassword' => 'NewPassword456'
        ]);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testFailsWhenNotAuthenticated(): void
    {
        $response = $this->request('PATCH', "/api/users/" . Uuid::v4()->toString() . "/change-password", [
            'oldPassword' => 'OldPassword123',
            'newPassword' => 'NewSecurePassword456'
        ]);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
