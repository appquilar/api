<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Uid\Uuid;

class GetCompanyBySlugApiTest extends IntegrationTestCase
{
    public function testRetrieveCompanyAsWorker(): void
    {
        $ownerId = Uuid::v4();
        $companyId = Uuid::v4();
        $slug = 'acme-inc';
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenACompanyWithOwnerAndIdAndSlug($ownerId, $companyId, $slug);

        $response = $this->request('GET', '/api/companies/' . $slug);
        $responseContent = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseContent);
        $this->assertArrayHasKey('phone_number', $responseContent['data']);
        $this->assertEquals($companyId->toString(), $responseContent['data']['company_id']);
        $this->assertEquals($slug, $responseContent['data']['slug']);
    }

    public function testRetrieveOtherOwnerCompanyAsAdmin(): void
    {
        $ownerId = Uuid::v4();
        $companyId = Uuid::v4();
        $slug = 'acme-inc';
        $this->givenImLoggedInAsAdmin();
        $this->givenACompanyWithOwnerAndIdAndSlug($ownerId, $companyId, $slug);

        $response = $this->request('GET', '/api/companies/' . $slug);
        $responseContent = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseContent);
        $this->assertArrayHasKey('phone_number', $responseContent['data']);
        $this->assertEquals($companyId->toString(), $responseContent['data']['company_id']);
        $this->assertEquals($slug, $responseContent['data']['slug']);
    }

    public function testRetrieveOtherUsersCompanyAsRegularUser(): void
    {
        $userId = Uuid::v4();
        $ownerId = Uuid::v4();
        $companyId = Uuid::v4();
        $slug = 'acme-inc';
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenACompanyWithOwnerAndIdAndSlug($ownerId, $companyId, $slug);

        $response = $this->request('GET', '/api/companies/' . $slug);
        $responseContent = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseContent);
        $this->assertArrayNotHasKey('phone_number', $responseContent['data']);
        $this->assertEquals($companyId->toString(), $responseContent['data']['company_id']);
        $this->assertEquals($slug, $responseContent['data']['slug']);
    }
}
