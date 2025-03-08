<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Uid\Uuid;

class GetCompanyByIdApiTest extends IntegrationTestCase
{
    public function testRetrieveCompanyAsWorker(): void
    {
        $ownerId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenACompanyWithOwnerAndId($ownerId, $companyId);

        $response = $this->request('GET', '/api/companies/'.$companyId->toString());
        $responseContent = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseContent);
        $this->assertArrayHasKey('phone_number', $responseContent['data']);
        $this->assertEquals($companyId->toString(), $responseContent['data']['company_id']);
    }

    public function testRetrieveOtherOwnerCompanyAsAdmin(): void
    {
        $ownerId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsAdmin();
        $this->givenACompanyWithOwnerAndId($ownerId, $companyId);

        $response = $this->request('GET', '/api/companies/'.$companyId->toString());
        $responseContent = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseContent);
        $this->assertArrayHasKey('phone_number', $responseContent['data']);
        $this->assertEquals($companyId->toString(), $responseContent['data']['company_id']);
    }

    public function testRetrieveOtherOwnerCompanyAsRegularUser(): void
    {
        $userId = Uuid::v4();
        $ownerId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenACompanyWithOwnerAndId($ownerId, $companyId);

        $response = $this->request('GET', '/api/companies/'.$companyId->toString());
        $responseContent = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseContent);
        $this->assertArrayNotHasKey('phone_number', $responseContent['data']);
        $this->assertEquals($companyId->toString(), $responseContent['data']['company_id']);
    }
}
