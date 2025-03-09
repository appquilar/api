<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class CompanyUserAcceptInvitationApiTest extends IntegrationTestCase
{
    public function testAcceptsInvitationSuccessfully(): void
    {
        $companyId = Uuid::v4();
        $token = 'valid-token';
        $this->givenACompanyWithId($companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::CONTRIBUTOR, CompanyUserStatus::PENDING, token: $token);

        $response = $this->request(
            'POST',
            "/api/companies/{$companyId}/invitations/{$token}/accept",
            [
                'email' => 'user@example.com',
                'password' => 'SecurePass123'
            ]
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testReturnsBadRequestIfAlreadyAccepted(): void
    {
        $companyId = Uuid::v4();
        $token = 'valid-token';
        $this->givenACompanyWithId($companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::CONTRIBUTOR, CompanyUserStatus::ACCEPTED, token: $token);

        $response = $this->request(
            'POST',
            "/api/companies/{$companyId}/invitations/{$token}/accept",
            [
                'email' => 'user@example.com',
                'password' => 'SecurePass123'
            ]
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
