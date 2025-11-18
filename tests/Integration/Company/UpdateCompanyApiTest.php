<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class UpdateCompanyApiTest extends IntegrationTestCase
{
    public function testUpdateCompanyHappyPath(): void
    {
        $userId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenACompanyWithOwnerAndId($userId, $companyId);

        $payload = [
            'name' => 'new name',
            'description' => 'new description',
            'slug' => 'new-slug',
            'fiscal_identifier' => 'new fiscal identifier',
            'contact_email' => 'new contact email',
            'phone_number_country_code' => 'ES',
            'phone_number_prefix' => '+34',
            'phone_number_number' => '666000000',
            'address' => [
                'street' => 'Fake st',
                'street2' => 'number 123',
                'city' => 'Springfield',
                'postal_code' => '1234',
                'state' => 'Iowa',
                'country' => 'US'
            ],
            'geoLocation' => [
                'latitude' => 45.78,
                'longitude' => -123.45,
            ]
        ];

        $response = $this->request('PATCH', '/api/companies/' . $companyId->toString(), $payload);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testIUpdateACompanyBeingAdmin(): void
    {
        $userId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsAdmin();
        $this->givenACompanyWithOwnerAndId($userId, $companyId);

        $payload = [
            'name' => 'new name',
            'description' => 'new description',
            'slug' => 'new-slug',
            'fiscal_identifier' => 'new fiscal identifier',
            'contact_email' => 'new contact email',
            'phone_number_country_code' => 'ES',
            'phone_number_prefix' => '+34',
            'phone_number_number' => '666000000',
            'address' => [
                'street' => 'Fake st',
                'street2' => 'number 123',
                'city' => 'Springfield',
                'postal_code' => '1234',
                'state' => 'Iowa',
                'country' => 'US'
            ],
            'geoLocation' => [
                'latitude' => 45.78,
                'longitude' => -123.45,
            ]
        ];

        $response = $this->request('PATCH', '/api/companies/' . $companyId->toString(), $payload);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testTryToUpdateACompanyWorkingOnItButNotBeingAdmin(): void
    {
        $userId = Uuid::v4();
        $ownerId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenACompanyWithOwnerAndId($ownerId, $companyId);
        $this->aCompanyHasAnUser($companyId, CompanyUserRole::CONTRIBUTOR, CompanyUserStatus::ACCEPTED, $userId);

        $payload = [
            'name' => 'new name',
            'description' => 'new description',
            'slug' => 'new-slug',
            'fiscal_identifier' => 'new fiscal identifier',
            'contact_email' => 'new contact email',
            'phone_number_country_code' => 'ES',
            'phone_number_prefix' => '+34',
            'phone_number_number' => '666000000',
            'address' => [
                'street' => 'Fake st',
                'street2' => 'number 123',
                'city' => 'Springfield',
                'postal_code' => '1234',
                'state' => 'Iowa',
                'country' => 'US'
            ],
            'location' => [
                'latitude' => 45.78,
                'longitude' => -123.45,
            ]
        ];

        $response = $this->request('PATCH', '/api/companies/' . $companyId->toString(), $payload);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
