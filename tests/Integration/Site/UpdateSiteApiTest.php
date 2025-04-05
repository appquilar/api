<?php

declare(strict_types=1);

namespace App\Tests\Integration\Site;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class UpdateSiteApiTest extends IntegrationTestCase
{
    public function testUpdateSiteSuccessfully(): void
    {
        $this->givenImLoggedInAsAdmin();

        $siteId = Uuid::v4();

        $this->givenItExistsASiteWithId($siteId);
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

        $response = $this->request('PATCH', '/api/sites/' . $siteId->toString(),$payload);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testUpdateSiteNonExistentImage(): void
    {
        $this->givenImLoggedInAsAdmin();

        $siteId = Uuid::v4();

        $this->givenItExistsASiteWithId($siteId);
        $logoId = Uuid::v4();
        $faviconId = Uuid::v4();
        $category1Id = Uuid::v4();
        $category2Id = Uuid::v4();

        $this->givenItExistsAnImageWithId($logoId);
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

        $response = $this->request('PATCH', '/api/sites/' . $siteId->toString(),$payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertErrorMessageExists('faviconId', json_decode($response->getContent(), true));
    }

    public function testUpdateSiteNonExistentCategory(): void
    {
        $this->givenImLoggedInAsAdmin();

        $siteId = Uuid::v4();

        $this->givenItExistsASiteWithId($siteId);
        $logoId = Uuid::v4();
        $faviconId = Uuid::v4();
        $category1Id = Uuid::v4();
        $category2Id = Uuid::v4();

        $this->givenItExistsAnImageWithId($logoId);
        $this->givenItExistsAnImageWithId($faviconId);
        $this->givenItExistsACategoryWithId($category1Id);

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

        $response = $this->request('PATCH', '/api/sites/' . $siteId->toString(),$payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertErrorMessageExists('categoryIds', json_decode($response->getContent(), true));
    }

    public function testUpdateSiteNonExistentSite(): void
    {
        $this->givenImLoggedInAsAdmin();

        $siteId = Uuid::v4();

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

        $response = $this->request('PATCH', '/api/sites/' . $siteId->toString(),$payload);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey(0, $content['error']);
        $this->assertEquals('Entity with id ' . $siteId->toString() . ' not found', $content['error'][0]);
    }
}
