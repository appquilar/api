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
        $parentId = Uuid::v4();
        $imageId = Uuid::v4();
        $existentSlug = 'existent-slug';
        $this->givenImLoggedInAsAdmin();
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenItExistsACategoryWithSlug($existentSlug);
        $this->givenItExistsAnImageWithId($imageId);
        $this->givenItExistsACategoryWithId($parentId);

        $payload = [
            'name' => 'Electronics',
            'slug' => $existentSlug,
            'description' => 'All kinds of electronic devices',
            'icon_id' => $imageId,
            'parent_id' => $parentId,
            'featured_image_id' => $imageId,
            'landscape_image_id' => $imageId
        ];

        $response = $this->request('PATCH', '/api/categories/' . $categoryId->toString(), $payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
