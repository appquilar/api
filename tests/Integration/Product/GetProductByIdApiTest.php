<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetProductByIdApiTest extends IntegrationTestCase
{
    public function test_get_product_by_id_happy_path(): void
    {
        $userId = Uuid::v4();
        $productId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $userId);

        $response = $this->request(
            'GET',
            '/api/products/' . $productId->toString(),
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('id', $productId->toString(), $responseBody);
    }

    public function test_try_to_get_product_without_permission(): void
    {
        $userId = Uuid::v4();
        $anotherUserId = Uuid::v4();
        $productId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($anotherUserId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $userId);

        $response = $this->request(
            'GET',
            '/api/products/' . $productId->toString(),
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_get_product_by_id_product_not_found(): void
    {
        $userId = Uuid::v4();
        $productId = Uuid::v4();
        $anotherProductId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $userId);

        $response = $this->request(
            'GET',
            '/api/products/' . $anotherProductId->toString(),
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
