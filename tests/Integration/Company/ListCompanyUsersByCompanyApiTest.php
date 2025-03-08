<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Uid\Uuid;

class ListCompanyUsersByCompanyApiTest extends IntegrationTestCase
{
    public function testRetrieveCompanyUsers(): void
    {
        $companyId = Uuid::v4();
        $ownerId = Uuid::v4();
        $otherUserId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenACompanyWithId($companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::ADMIN, CompanyUserStatus::ACCEPTED, $ownerId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::CONTRIBUTOR, CompanyUserStatus::ACCEPTED, $otherUserId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::CONTRIBUTOR, CompanyUserStatus::PENDING);

        $response = $this->request('GET', '/api/companies/' . $companyId->toString() . '/users');
        $responseContent = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($responseContent['success']);
        $this->assertArrayHasKey('data', $responseContent);
        $this->assertCount(3, $responseContent['data']);
        $this->assertEquals(3, $responseContent['total']);
        $this->assertEquals(1, $responseContent['page']);
    }
}
