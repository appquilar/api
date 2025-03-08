<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class AddUserToCompanyApiTest extends IntegrationTestCase
{
    public function testAddingANonexistentUserAsAnCompanyAdmin(): void
    {
        $userId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenACompanyWithOwnerAndId($userId, $companyId);

        $payload = [
            'email' => 'newuser@example.com',
            'role' => CompanyUserRole::CONTRIBUTOR->value
        ];

        $response = $this->request('POST', '/api/companies/' . $companyId->toString() . '/users', $payload);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testTryingToAddAnUserBeingContributorReturnsUnauthorized(): void
    {
        $userId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenACompanyWithId($companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::CONTRIBUTOR, CompanyUserStatus::ACCEPTED, $userId);

        $payload = [
            'email' => 'newuser@example.com',
            'role' => CompanyUserRole::CONTRIBUTOR->value
        ];

        $response = $this->request('POST', '/api/companies/' . $companyId->toString() . '/users', $payload);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testTryToAddUserBeingCompanyAdminButWithPendingInvitation(): void
    {
        $userId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenACompanyWithId($companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::ADMIN, CompanyUserStatus::PENDING, $userId);

        $payload = [
            'email' => 'newuser@example.com',
            'role' => CompanyUserRole::CONTRIBUTOR->value
        ];

        $response = $this->request('POST', '/api/companies/' . $companyId->toString() . '/users', $payload);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
