<?php

declare(strict_types=1);

namespace App\Tests\Integration\Site;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class CreateSiteApiTest extends IntegrationTestCase
{
    public function testCreateSiteSuccessfully(): void
    {
        $this->givenImLoggedInAsAdmin();

        $logoId = Uuid::v4();
        $faviconId = Uuid::v4();
        $category1Id = Uuid::v4();
        $category2Id = Uuid::v4();

        $this->givenItExistsAnImageWithId($logoId);
        $this->givenItExistsAnImageWithId($faviconId);
        $this->givenItExistsACategoryWithId($category1Id);
        $this->givenItExistsACategoryWithId($category2Id);

        $payload = [
            "site_id" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "title" => "Electronics",
            "url" => "https://electronics.com",
            "description" => "All kinds of electronic devices",
            "logo_id" => $logoId->toString(),
            "favicon_id" => $faviconId->toString(),
            "primary_color" => "000000",
            "category_ids" => [$category1Id, $category2Id],
            "menu_category_ids" => [$category1Id],
            "featured_category_ids" => [$category2Id],
        ];

        $response = $this->request('POST', '/api/sites',$payload);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testTryingToCreateSiteWithNonExistingImage(): void
    {
        $this->givenImLoggedInAsAdmin();

        $logoId = Uuid::v4();
        $faviconId = Uuid::v4();
        $category1Id = Uuid::v4();
        $category2Id = Uuid::v4();

        $this->givenItExistsAnImageWithId($faviconId);
        $this->givenItExistsACategoryWithId($category1Id);
        $this->givenItExistsACategoryWithId($category2Id);

        $payload = [
            "site_id" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "title" => "Electronics",
            "url" => "https://electronics.com",
            "description" => "All kinds of electronic devices",
            "logo_id" => $logoId->toString(),
            "favicon_id" => $faviconId->toString(),
            "primary_color" => "000000",
            "category_ids" => [$category1Id, $category2Id],
            "menu_category_ids" => [$category1Id],
            "featured_category_ids" => [$category2Id],
        ];

        $response = $this->request('POST', '/api/sites',$payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertErrorMessageExists('logoId', json_decode($response->getContent(), true));
    }

    public function testTryingToCreateSiteWithNonExistingCategory(): void
    {
        $this->givenImLoggedInAsAdmin();

        $logoId = Uuid::v4();
        $faviconId = Uuid::v4();
        $category1Id = Uuid::v4();
        $category2Id = Uuid::v4();

        $this->givenItExistsAnImageWithId($logoId);
        $this->givenItExistsAnImageWithId($faviconId);
        $this->givenItExistsACategoryWithId($category2Id);

        $payload = [
            "site_id" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "title" => "Electronics",
            "url" => "https://electronics.com",
            "description" => "All kinds of electronic devices",
            "logo_id" => $logoId->toString(),
            "favicon_id" => $faviconId->toString(),
            "primary_color" => "000000",
            "category_ids" => [$category1Id, $category2Id],
            "menu_category_ids" => [$category1Id],
            "featured_category_ids" => [$category2Id],
        ];

        $response = $this->request('POST', '/api/sites',$payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertErrorMessageExists('categoryIds', json_decode($response->getContent(), true));
    }

    public function testTryToCreateASiteBeingARegularUser(): void
    {
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());

        $logoId = Uuid::v4();
        $faviconId = Uuid::v4();
        $category1Id = Uuid::v4();
        $category2Id = Uuid::v4();

        $this->givenItExistsAnImageWithId($logoId);
        $this->givenItExistsAnImageWithId($faviconId);
        $this->givenItExistsACategoryWithId($category1Id);
        $this->givenItExistsACategoryWithId($category2Id);

        $payload = [
            "site_id" => Uuid::v4()->toString(),
            "name" => "Electronics",
            "title" => "Electronics",
            "url" => "https://electronics.com",
            "description" => "All kinds of electronic devices",
            "logo_id" => $logoId->toString(),
            "favicon_id" => $faviconId->toString(),
            "primary_color" => "000000",
            "category_ids" => [$category1Id, $category2Id],
            "menu_category_ids" => [$category1Id],
            "featured_category_ids" => [$category2Id],
        ];

        $response = $this->request('POST', '/api/sites',$payload);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
