<?php declare(strict_types=1);

namespace App\Tests\Integration\Category;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ListCategoryBreadcrumbsApiTest extends IntegrationTestCase
{
    public function test_list_category_breadcrumbs(): void
    {
        $categoryId = Uuid::v4();
        $parentId = Uuid::v4();
        $grandParentId = Uuid::v4();
        $anotherCategoryId = Uuid::v4();
        $this->givenItExistsACategoryWithIdAndParentId($grandParentId);
        $this->givenItExistsACategoryWithIdAndParentId($parentId, $grandParentId);
        $this->givenItExistsACategoryWithIdAndParentId($categoryId, $parentId);
        $this->givenItExistsACategoryWithIdAndParentId($anotherCategoryId, $parentId);

        $response = $this->request('GET', '/api/categories/' . $categoryId->toString() . '/breadcrumbs');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseBody);
        $this->assertCount(3, $responseBody['data']);

        $response = $this->request('GET', '/api/categories/' . $parentId->toString() . '/breadcrumbs');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseBody);
        $this->assertCount(2, $responseBody['data']);

        $response = $this->request('GET', '/api/categories/' . $grandParentId->toString() . '/breadcrumbs');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseBody);
        $this->assertCount(1, $responseBody['data']);

        $response = $this->request('GET', '/api/categories/' . $anotherCategoryId->toString() . '/breadcrumbs');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseBody);
        $this->assertCount(3, $responseBody['data']);
    }
}
