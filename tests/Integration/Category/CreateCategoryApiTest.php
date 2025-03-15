<?php

declare(strict_types=1);

namespace App\Tests\Integration\Category;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class CreateCategoryApiTest extends IntegrationTestCase
{
    public function testCreateCategorySuccessfully(): void
    {
        $this->givenImLoggedInAsAdmin();

        $payload = [
            "categoryId" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "description" => "All kinds of electronic devices",
            "parentId" => null,
            "iconId" => null,
            "featuredImageId" => null,
            "landscapeImageId" => null
        ];

        $response = $this->request('POST', '/api/categories',$payload);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testCreateCategoryValidationFails(): void
    {
        $this->givenImLoggedInAsAdmin();

        $payload = [
            "categoryId" => null,
            "name" => "",
            "description" => "",
        ];

        $response = $this->request('POST', '/api/categories',$payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testCreateCategoryWithoutBeingLoggedIn(): void
    {
        $payload = [
            "categoryId" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "description" => "All kinds of electronic devices",
            "parentId" => null,
            "iconId" => null,
            "featuredImageId" => null,
            "landscapeImageId" => null
        ];

        $response = $this->request('POST', '/api/categories',$payload);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCreateCategoryBeingOnlyRegularUser(): void
    {
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());
        $payload = [
            "categoryId" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "description" => "All kinds of electronic devices",
            "parentId" => null,
            "iconId" => null,
            "featuredImageId" => null,
            "landscapeImageId" => null
        ];

        $response = $this->request('POST', '/api/categories',$payload);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testAddingAParentIdWithoutExistingTheCategory(): void
    {
        $this->givenImLoggedInAsAdmin();

        $payload = [
            "categoryId" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "description" => "All kinds of electronic devices",
            "parentId" => Uuid::v4()->toString(),
            "iconId" => null,
            "featuredImageId" => null,
            "landscapeImageId" => null
        ];

        $response = $this->request('POST', '/api/categories',$payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertErrorMessageExists('parentId', json_decode($response->getContent(), true));
    }

    public function testAddingParentIdWithExistingCategory(): void
    {
        $parentId = Uuid::v4();
        $this->givenItExistsACategoryWithId($parentId);
        $this->givenImLoggedInAsAdmin();

        $payload = [
            "categoryId" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "description" => "All kinds of electronic devices",
            "parentId" => $parentId->toString(),
            "iconId" => null,
            "featuredImageId" => null,
            "landscapeImageId" => null
        ];

        $response = $this->request('POST', '/api/categories',$payload);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testAddingAnImageWithANonExistingImage(): void
    {
        $this->givenImLoggedInAsAdmin();

        $payload = [
            "categoryId" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "description" => "All kinds of electronic devices",
            "parentId" => null,
            "iconId" => Uuid::v4(),
            "featuredImageId" => null,
            "landscapeImageId" => null
        ];

        $response = $this->request('POST', '/api/categories',$payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertErrorMessageExists('iconId', json_decode($response->getContent(), true));
    }

    public function testAddingAnImageWithAnExistingImage(): void
    {
        $iconId = Uuid::v4();
        $this->givenItExistsAnImageWithId($iconId);
        $this->givenImLoggedInAsAdmin();

        $payload = [
            "categoryId" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "description" => "All kinds of electronic devices",
            "parentId" => null,
            "iconId" => $iconId->toString(),
            "featuredImageId" => null,
            "landscapeImageId" => null
        ];

        $response = $this->request('POST', '/api/categories',$payload);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testCreateACategoryWithAnAlreadyExistingSlug(): void
    {
        $this->givenImLoggedInAsAdmin();
        $this->givenItExistsACategoryWithSlug('name');

        $payload = [
            "categoryId" => Uuid::v4()->toString(),
            "name" => "Name",
            "description" => "All kinds of electronic devices",
            "parentId" => null,
            "iconId" => null,
            "featuredImageId" => null,
            "landscapeImageId" => null
        ];

        $response = $this->request('POST', '/api/categories',$payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testCreateACategoryWithAnUniqueSlug(): void
    {
        $this->givenImLoggedInAsAdmin();
        $this->givenItExistsACategoryWithSlug('name');

        $payload = [
            "categoryId" => Uuid::v4()->toString(),
            "name" => "Name 2",
            "description" => "All kinds of electronic devices",
            "parentId" => null,
            "iconId" => null,
            "featuredImageId" => null,
            "landscapeImageId" => null
        ];

        $response = $this->request('POST', '/api/categories',$payload);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }
}
