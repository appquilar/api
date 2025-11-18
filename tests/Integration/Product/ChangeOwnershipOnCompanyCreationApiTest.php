<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ChangeOwnershipOnCompanyCreationApiTest extends IntegrationTestCase
{
    public function test_change_ownership_from_user_to_company_on_company_creation(): void
    {
        $userId = Uuid::v4();
        $productId1 = Uuid::v4();
        $productId2 = Uuid::v4();
        $productId3 = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId1, $userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId2, $userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId3, $userId);

        $response = $this->request(
            'GET',
            '/api/users/' . $userId->toString() . '/products',
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('total', 3, $responseBody);

        $companyId = Uuid::v4();

        $response = $this->request(
            'POST',
            '/api/companies',
            [
                'company_id' => $companyId->toString(),
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

        $response = $this->request(
            'GET',
            '/api/companies/' . $companyId->toString() . '/products',
        );
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('total', 3, $responseBody);

        $response = $this->request(
            'GET',
            '/api/users/' . $userId->toString() . '/products',
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('total', 0, $responseBody);
    }
}
