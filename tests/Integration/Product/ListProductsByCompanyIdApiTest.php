<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ListProductsByCompanyIdApiTest extends ProductIntegrationTestCase
{
    public function test_list_products_happy_path(): void
    {
        $userId = Uuid::v4();
        $this->givenAnUserWithIdAndEmail($userId, 'a@a.com');
        $productId1 = Uuid::v4();
        $productId2 = Uuid::v4();
        $productId3 = Uuid::v4();
        $productId4 = Uuid::v4();
        $companyId = Uuid::v4();
        $anotherCompanyId = Uuid::v4();
        $this->givenACompanyWithId($companyId);
        $this->givenACompanyWithId($anotherCompanyId);
        $this->givenItExistsAProductWithIdBelongingToACompanyId($productId1, $companyId);
        $this->givenItExistsAProductWithIdBelongingToACompanyId($productId2, $companyId);
        $this->givenItExistsAProductWithIdBelongingToACompanyId($productId3, $companyId);
        $this->givenItExistsAProductWithIdBelongingToACompanyId($productId4, $anotherCompanyId);

        $response = $this->request(
            'GET',
            '/api/companies/' . $companyId->toString() . '/products?page=1&per_page=1',
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseBody);
        $this->assertArrayHasKey('data', $responseBody['data']);
        $this->assertCount(1, $responseBody['data']['data']);
        $this->assertArrayHasKeyAndValue('total', 3, $responseBody);

        $response = $this->request(
            'GET',
            '/api/companies/' . $companyId->toString() . '/products?page=4&per_page=1',
        );
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseBody);
        $this->assertArrayHasKey('data', $responseBody['data']);
        $this->assertCount(0, $responseBody['data']['data']);
        $this->assertArrayHasKeyAndValue('total', 3, $responseBody);

        $response = $this->request(
            'GET',
            '/api/companies/' . $companyId->toString() . '/products?page=2&per_page=2',
        );
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseBody);
        $this->assertArrayHasKey('data', $responseBody['data']);
        $this->assertCount(1, $responseBody['data']['data']);
        $this->assertArrayHasKeyAndValue('total', 3, $responseBody);
    }
}
