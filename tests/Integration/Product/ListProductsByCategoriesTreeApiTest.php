<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ListProductsByCategoriesTreeApiTest extends ProductIntegrationTestCase
{
    public function test_list_products_from_different_categories(): void
    {
        $userId = Uuid::v4();
        $this->givenAnUserWithIdAndEmail($userId, 'a@a.com');
        $categoryId = Uuid::v4();
        $categoryId2 = Uuid::v4();
        $parentId = Uuid::v4();
        $parentId2 = Uuid::v4();
        $grandParentId = Uuid::v4();
        $unrelatedCategoryId = Uuid::v4();
        $productId1 = Uuid::v4();
        $productId2 = Uuid::v4();
        $productId3 = Uuid::v4();
        $productId4 = Uuid::v4();
        $productId5 = Uuid::v4();
        $productId6 = Uuid::v4();
        $productId7 = Uuid::v4();
        $productId8 = Uuid::v4();
        $productId9 = Uuid::v4();
        $productId10 = Uuid::v4();
        $this->givenItExistsACategoryWithId($grandParentId);
        $this->givenItExistsACategoryWithId($unrelatedCategoryId);
        $this->givenItExistsACategoryWithIdAndParentId($parentId, $grandParentId);
        $this->givenItExistsACategoryWithIdAndParentId($parentId2, $grandParentId);
        $this->givenItExistsACategoryWithIdAndParentId($categoryId, $parentId);
        $this->givenItExistsACategoryWithIdAndParentId($categoryId2, $parentId2);
        $this->givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser($productId1, $grandParentId, $userId);
        $this->givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser($productId2, $grandParentId, $userId);
        $this->givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser($productId3, $grandParentId, $userId);
        $this->givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser($productId4, $grandParentId, $userId);
        $this->givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser($productId5, $parentId, $userId);
        $this->givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser($productId6, $parentId, $userId);
        $this->givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser($productId7, $parentId2, $userId);
        $this->givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser($productId8, $categoryId, $userId);
        $this->givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser($productId9, $categoryId2, $userId);
        $this->givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser($productId10, $unrelatedCategoryId, $userId);

        $response = $this->request(
            'GET',
            '/api/categories/' . $grandParentId->toString() . '/products?page=1&per_page=1',
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('total', 9, $responseBody);

        $response = $this->request(
            'GET',
            '/api/categories/' . $parentId->toString() . '/products?page=1&per_page=1',
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('total', 3, $responseBody);

        $response = $this->request(
            'GET',
            '/api/categories/' . $categoryId->toString() . '/products?page=1&per_page=1',
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('total', 1, $responseBody);

        $response = $this->request(
            'GET',
            '/api/categories/' . $unrelatedCategoryId->toString() . '/products?page=1&per_page=1',
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('total', 1, $responseBody);

        $response = $this->request(
            'GET',
            '/api/categories/' . $parentId2->toString() . '/products?page=1&per_page=1',
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('total', 2, $responseBody);
    }
}
