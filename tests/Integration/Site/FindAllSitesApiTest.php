<?php

declare(strict_types=1);

namespace App\Tests\Integration\Site;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class FindAllSitesApiTest extends IntegrationTestCase
{
    public function testReturnSites(): void
    {
        $this->givenImLoggedInAsAdmin();

        $site1 = Uuid::v4();
        $site2 = Uuid::v4();
        $site3 = Uuid::v4();

        $this->givenItExistsASiteWithId($site1);
        $this->givenItExistsASiteWithId($site2);
        $this->givenItExistsASiteWithId($site3);

        $response = $this->request('GET', '/api/sites');
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];
        $this->assertCount(3, $data);
        $this->assertArrayHasKey('site_id', $data[0]);
        $this->assertArrayHasKey('site_id', $data[1]);
        $this->assertArrayHasKey('site_id', $data[2]);
        $this->assertEquals($site1->toString(), $data[0]['site_id']);
        $this->assertEquals($site2->toString(), $data[1]['site_id']);
        $this->assertEquals($site3->toString(), $data[2]['site_id']);
    }

    public function testFindAllSitesNoSites(): void
    {
        $this->givenImLoggedInAsAdmin();

        $response = $this->request('GET', '/api/sites');
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];
        $this->assertCount(0, $data);
    }

    public function testFindAllSitesAsUser(): void
    {
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());

        $response = $this->request('GET', '/api/sites');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
