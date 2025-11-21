<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ListProductsByUserIdApiTest extends ProductIntegrationTestCase
{
    public function test_list_products_happy_path(): void
    {
        $productId1 = Uuid::v4();
        $productId2 = Uuid::v4();
        $productId3 = Uuid::v4();
        $productId4 = Uuid::v4();
        $userId = Uuid::v4();
        $anotherUserId = Uuid::v4();
        $this->givenAnUserWithIdAndEmail($userId, 'a@a.com');
        $this->givenAnUserWithIdAndEmail($anotherUserId, 'b@a.com');
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId1, $userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId2, $userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId3, $userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId4, $anotherUserId);

        $response = $this->request(
            'GET',
            '/api/users/' . $userId->toString() . '/products?page=1&per_page=1',
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseBody);
        $this->assertArrayHasKey('data', $responseBody['data']);
        $this->assertCount(1, $responseBody['data']['data']);
        $this->assertArrayHasKeyAndValue('total', 3, $responseBody);

        $response = $this->request(
            'GET',
            '/api/users/' . $userId->toString() . '/products?page=4&per_page=1',
        );
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseBody);
        $this->assertArrayHasKey('data', $responseBody['data']);
        $this->assertCount(0, $responseBody['data']['data']);
        $this->assertArrayHasKeyAndValue('total', 3, $responseBody);

        $response = $this->request(
            'GET',
            '/api/users/' . $userId->toString() . '/products?page=2&per_page=2',
        );
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseBody);
        $this->assertArrayHasKey('data', $responseBody['data']);
        $this->assertCount(1, $responseBody['data']['data']);
        $this->assertArrayHasKeyAndValue('total', 3, $responseBody);
    }
}
