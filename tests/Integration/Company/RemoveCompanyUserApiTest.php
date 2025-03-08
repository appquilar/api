<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class RemoveCompanyUserApiTest extends IntegrationTestCase
{
    public function testRemovingUserFromCompanyHappyPath(): void
    {
        $ownerId = Uuid::v4();
        $companyId = Uuid::v4();
        $userId = Uuid::v4();

        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenACompanyWithOwnerAndId($ownerId, $companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::CONTRIBUTOR, CompanyUserStatus::ACCEPTED, $userId);

        $response = $this->request('DELETE', "/api/companies/{$companyId->toString()}/users/{$userId->toString()}");

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
