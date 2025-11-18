<?php

declare(strict_types=1);

namespace App\Tests\Integration\Category;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class UpdateCategoryApiTest extends IntegrationTestCase
{
    public function testUpdateCategory(): void
    {
        $categoryId = Uuid::v4();
        $parentId = Uuid::v4();
        $imageId = Uuid::v4();
        $this->givenImLoggedInAsAdmin();
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenItExistsAnImageWithId($imageId);
        $this->givenItExistsACategoryWithId($parentId);

        $payload = [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'All kinds of electronic devices',
            'icon_id' => $imageId,
            'parent_id' => $parentId,
            'featured_image_id' => $imageId,
            'landscape_image_id' => $imageId
        ];

        $response = $this->request('PATCH', '/api/categories/' . $categoryId->toString(), $payload);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testUpdateCategorySameSlug(): void
    {
        $categoryId = Uuid::v4();
        $parentId = Uuid::v4();
        $imageId = Uuid::v4();
        $slug = 'electronics';
        $this->givenImLoggedInAsAdmin();
        $this->givenItExistsACategoryWithIdAndSlug($categoryId, $slug);
        $this->givenItExistsAnImageWithId($imageId);
        $this->givenItExistsACategoryWithId($parentId);

        $payload = [
            'name' => 'Electronics',
            'slug' => $slug,
            'description' => 'All kinds of electronic devices',
            'icon_id' => $imageId,
            'parent_id' => $parentId,
            'featured_image_id' => $imageId,
            'landscape_image_id' => $imageId
        ];

        $response = $this->request('PATCH', '/api/categories/' . $categoryId->toString(), $payload);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testTryToUpdateNonexistentCategory(): void
    {
        $categoryId = Uuid::v4();
        $this->givenImLoggedInAsAdmin();

        $payload = [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'All kinds of electronic devices',
            'icon_id' => null,
            'parent_id' => null,
            'featured_image_id' => null,
            'landscape_image_id' => null
        ];

        $response = $this->request('PATCH', '/api/categories/' . $categoryId->toString(), $payload);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testTryToUpdateACategoryWithAnAlreadyExistingSlug(): void
    {
        $categoryId = Uuid::v4();
        $anotherCategoryId = Uuid::v4();
        $parentId = Uuid::v4();
        $imageId = Uuid::v4();
        $existentSlug = 'existent-slug';
        $parentSlug = 'parent-slug';
        $this->givenImLoggedInAsAdmin();
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenItExistsACategoryWithIdAndParentIdAndSlug($anotherCategoryId, $parentId, $existentSlug);
        $this->givenItExistsAnImageWithId($imageId);
        $this->givenItExistsACategoryWithIdAndSlug($parentId, $parentSlug);

        $payload = [
            'name' => 'Existent Slug',
            'description' => 'All kinds of electronic devices',
            'icon_id' => $imageId,
            'parent_id' => $parentId,
            'featured_image_id' => $imageId,
            'landscape_image_id' => $imageId
        ];

        $response = $this->request('PATCH', '/api/categories/' . $categoryId->toString(), $payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_try_to_update_a_category_causing_a_circular_parent_id_reference(): void
    {
        $categoryId = Uuid::v4();
        $parentId = Uuid::v4();
        $grandParentId = Uuid::v4();
        $imageId = Uuid::v4();
        $this->givenImLoggedInAsAdmin();
        $this->givenItExistsAnImageWithId($imageId);
        $this->givenItExistsACategoryWithId($grandParentId);
        $this->givenItExistsACategoryWithIdAndParentId($parentId, $grandParentId);
        $this->givenItExistsACategoryWithIdAndParentId($categoryId, $parentId);

        $payload = [
            'name' => 'Grandparent Slug',
            'description' => 'grandparent',
            'icon_id' => $imageId,
            'parent_id' => $categoryId,
            'featured_image_id' => $imageId,
            'landscape_image_id' => $imageId
        ];

        $response = $this->request('PATCH', '/api/categories/' . $grandParentId->toString(), $payload);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseBody);
        $this->assertArrayHasKey(0, $responseBody['error']);
        $this->assertEquals('category.update.parent_id.circular', $responseBody['error'][0]);
    }

    public function test_removing_parent_id_from_category(): void
    {
        $categoryId = Uuid::v4();
        $parentId = Uuid::v4();
        $imageId = Uuid::v4();
        $this->givenImLoggedInAsAdmin();
        $this->givenItExistsAnImageWithId($imageId);
        $this->givenItExistsACategoryWithId($parentId);
        $this->givenItExistsACategoryWithIdAndParentId($categoryId, $parentId);

        $payload = [
            'name' => 'Grandparent Slug',
            'description' => 'grandparent',
            'icon_id' => $imageId,
            'parent_id' => null,
            'featured_image_id' => $imageId,
            'landscape_image_id' => $imageId
        ];

        $response = $this->request('PATCH', '/api/categories/' . $categoryId->toString(), $payload);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_try_to_update_a_category_as_its_id_as_parent_id(): void
    {
        $categoryId = Uuid::v4();
        $parentId = Uuid::v4();
        $imageId = Uuid::v4();
        $this->givenImLoggedInAsAdmin();
        $this->givenItExistsAnImageWithId($imageId);
        $this->givenItExistsACategoryWithId($parentId);
        $this->givenItExistsACategoryWithIdAndParentId($categoryId, $parentId);

        $payload = [
            'name' => 'Grandparent Slug',
            'description' => 'grandparent',
            'icon_id' => $imageId,
            'parent_id' => $categoryId,
            'featured_image_id' => $imageId,
            'landscape_image_id' => $imageId
        ];

        $response = $this->request('PATCH', '/api/categories/' . $categoryId->toString(), $payload);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseBody);
        $this->assertArrayHasKey(0, $responseBody['error']);
        $this->assertEquals('category.update.parent_id.own_id_as_parent', $responseBody['error'][0]);
    }
}
