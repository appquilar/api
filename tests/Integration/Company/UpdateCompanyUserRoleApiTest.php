<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company;

use App\Company\Domain\Enum\CompanyUserStatus;
use App\Tests\Integration\IntegrationTestCase;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\CompanyUser;
use App\Company\Domain\Enum\CompanyUserRole;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\Response;

class UpdateCompanyUserRoleApiTest extends IntegrationTestCase
{
    public function testUpdatingRoleRequiresAdminPrivileges(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $companyAdminId = Uuid::v4();
        $payload = ['role' => CompanyUserRole::ADMIN->value];

        $this->givenImLoggedInAsRegularUserWithUserId($companyAdminId);
        $this->givenACompanyWithOwnerAndId($companyAdminId, $companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::CONTRIBUTOR, CompanyUserStatus::ACCEPTED, $userId);

        $response = $this->request(
            'PATCH',
            "/api/companies/{$companyId->toString()}/users/{$userId->toString()}",
            $payload
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testCannotUpdateRoleIfNotAnAdmin(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $userLoggedInId = Uuid::v4();
        $payload = ['role' => CompanyUserRole::ADMIN->value];

        $this->givenImLoggedInAsRegularUserWithUserId($userLoggedInId);
        $this->givenACompanyWithId($companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::CONTRIBUTOR, CompanyUserStatus::ACCEPTED, $userLoggedInId);

        $response = $this->request(
            'PATCH',
            "/api/companies/{$companyId->toString()}/users/{$userId->toString()}",
            $payload
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testTryToUpdateWithANonexistentRole(): void
    {
        $companyId = Uuid::v4();
        $userId = Uuid::v4();
        $companyAdminId = Uuid::v4();
        $payload = ['role' => 'nonexistent_role'];

        $this->givenImLoggedInAsRegularUserWithUserId($companyAdminId);
        $this->givenACompanyWithOwnerAndId($companyAdminId, $companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::CONTRIBUTOR, CompanyUserStatus::ACCEPTED, $userId);

        $response = $this->request(
            'PATCH',
            "/api/companies/{$companyId->toString()}/users/{$userId->toString()}",
            $payload
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
