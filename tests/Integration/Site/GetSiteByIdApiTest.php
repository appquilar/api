<?php

declare(strict_types=1);

namespace App\Tests\Integration\Site;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetSiteByIdApiTest extends IntegrationTestCase
{
    public function testGetExistentSiteById(): void
    {
        $siteId = Uuid::v4();

        $this->givenItExistsASiteWithId($siteId);

        $response = $this->request('GET', '/api/sites/' . $siteId->toString());
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];
        $this->assertEquals($siteId->toString(), $data['site_id']);
    }

    public function testGetNonExistentSiteById(): void
    {
        $siteId = Uuid::v4();

        $response = $this->request('GET', '/api/sites/' . $siteId->toString());
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey(0, $content['error']);
        $this->assertEquals('Entity with id ' . $siteId->toString() . ' not found', $content['error'][0]);
    }
}
