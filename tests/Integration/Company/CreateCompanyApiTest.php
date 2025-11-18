<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class CreateCompanyApiTest extends IntegrationTestCase
{
    public function testCreateCompanySuccess(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $response = $this->request(
            'POST',
            '/api/companies',
            [
                'company_id' => Uuid::v4()->toString(),
                'name' => 'Acme Inc.',
                'owner_id' => $userId->toString(),
                'description' => 'An innovative company',
                'fiscal_identifier' => '123456789',
                'contact_email' => 'contact@acme.com',
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
            ]
        );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testCreateCompanyValidationFails(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $response = $this->request(
            'POST',
            '/api/companies',
            [
                'company_id' => 'invalid-uuid',
                'name' => '',
                'ownerId' => '',
                'contact_email' => 'invalid-email',
            ]
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('errors', $responseData['error']);
        $this->assertArrayHasKey('companyId', $responseData['error']['errors']);
        $this->assertArrayHasKey('name', $responseData['error']['errors']);
        $this->assertArrayHasKey('ownerId', $responseData['error']['errors']);
    }

    public function testAnUserCantHaveMultipleCompanies(): void
    {
        $ownerId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);

        $response = $this->request(
            'POST',
            '/api/companies',
            [
                'company_id' => Uuid::v4()->toRfc4122(),
                'name' => 'Acme Inc.',
                'owner_id' => $ownerId,
                'description' => 'An innovative company',
                'fiscal_identifier' => '123456789',
                'contact_email' => 'contact@acme.com',
                'phone_number_country_code' => 'ES',
                'phone_number_prefix' => '+34',
                'phone_number_number' => '666000000',
            ]
        );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $response = $this->request(
            'POST',
            '/api/companies',
            [
                'company_id' => Uuid::v4()->toRfc4122(),
                'name' => 'Acme Inc. 2',
                'owner_id' => $ownerId,
                'description' => 'An innovative company',
                'fiscal_identifier' => '123456789',
                'contact_email' => 'contact@acme.com',
                'phone_number_country_code' => 'ES',
                'phone_number_prefix' => '+34',
                'phone_number_number' => '666000000',
            ]
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('ownerId', $responseData['error']['errors']);
        $this->assertEquals('company.create.owner_id.unique', $responseData['error']['errors']['ownerId'][0]);
    }

    public function testTryToCreateACompanyWithoutBeingLoggedIn(): void
    {
        $response = $this->request(
            'POST',
            '/api/companies',
            [
                'company_id' => Uuid::v4()->toRfc4122(),
                'name' => 'Acme Inc.',
                'owner_id' => Uuid::v4(),
                'description' => 'An innovative company',
                'fiscal_identifier' => '123456789',
                'contact_email' => 'contact@acme.com',
                'phone_number_country_code' => 'ES',
                'phone_number_prefix' => '+34',
                'phone_number_number' => '666000000',
            ]
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
