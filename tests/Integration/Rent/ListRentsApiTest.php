<?php declare(strict_types=1);

namespace App\Tests\Integration\Rent;

use App\Rent\Domain\Enum\RentStatus;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ListRentsApiTest extends IntegrationTestCase
{
    public function test_list_rents_returns_only_rents_for_logged_in_owner(): void
    {
        $ownerId        = Uuid::v4();
        $anotherOwnerId = Uuid::v4();
        $productId      = Uuid::v4();

        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);

        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-01-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-01-05 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-01-10 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-01-15 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $anotherOwnerId,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-01-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-01-05 10:00:00 Europe/Madrid'),
        ]);

        $response = $this->request(
            'GET',
            '/api/rents?page=1&per_page=10',
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('data', $content);
        $this->assertCount(2, $content['data']);

        $this->assertArrayHasKey('total', $content);
        $this->assertEquals(2, $content['total']);

        $ownerIds = array_unique(array_column($content['data'], 'owner_id'));
        $this->assertCount(1, $ownerIds);
        $this->assertSame($ownerId->toString(), $ownerIds[0]);
    }

    public function test_list_rents_can_be_filtered_by_product_id(): void
    {
        $ownerId         = Uuid::v4();
        $productIdMatch  = Uuid::v4();
        $productIdOther  = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);

        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productIdMatch,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-01-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-01-03 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productIdOther,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-01-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-01-03 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productIdOther,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-01-05 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-01-07 10:00:00 Europe/Madrid'),
        ]);

        $url = sprintf(
            '/api/rents?product_id=%s&page=1&per_page=10',
            $productIdMatch->toString()
        );

        $response = $this->request('GET', $url);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('data', $content);
        $this->assertCount(1, $content['data']);

        $this->assertArrayHasKey('total', $content);
        $this->assertEquals(1, $content['total']);

        $productIds = array_unique(array_column($content['data'], 'product_id'));
        $this->assertCount(1, $productIds);
        $this->assertSame($productIdMatch->toString(), $productIds[0]);
    }

    public function test_list_rents_can_be_filtered_by_date_range(): void
    {
        $ownerId   = Uuid::v4();
        $productId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);

        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-02-10 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-02-12 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-02-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-02-05 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-02-20 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-02-25 10:00:00 Europe/Madrid'),
        ]);

        $url = '/api/rents?start_date=2025-02-09 00:00:00 Europe/Madrid'
            . '&end_date=2025-02-15 23:59:59 Europe/Madrid'
            . '&page=1&per_page=10';

        $response = $this->request('GET', $url);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('data', $content);
        $this->assertCount(1, $content['data']);

        $this->assertArrayHasKey('total', $content);
        $this->assertEquals(1, $content['total']);

        $returned = $content['data'][0];
        $this->assertSame($productId->toString(), $returned['product_id']);
        $this->assertSame(RentStatus::PENDING->value, $returned['status']);
    }

    public function test_list_rents_can_be_filtered_by_status(): void
    {
        $ownerId   = Uuid::v4();
        $productId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);

        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-03-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-03-05 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::COMPLETED,
            'startDate' => new \DateTime('2025-03-10 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-03-15 10:00:00 Europe/Madrid'),
        ]);

        $url = sprintf(
            '/api/rents?status=%s&page=1&per_page=10',
            RentStatus::PENDING->value
        );

        $response = $this->request('GET', $url);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('data', $content);
        $this->assertCount(1, $content['data']);

        $this->assertArrayHasKey('total', $content);
        $this->assertEquals(1, $content['total']);

        $returned = $content['data'][0];
        $this->assertSame(RentStatus::PENDING->value, $returned['status']);
        $this->assertSame($productId->toString(), $returned['product_id']);
        $this->assertSame($ownerId->toString(), $returned['owner_id']);
    }

    public function test_list_rents_supports_pagination_with_multiple_filters(): void
    {
        $ownerId        = Uuid::v4();
        $otherOwnerId   = Uuid::v4();
        $productId      = Uuid::v4();
        $anotherProduct = Uuid::v4();

        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);

        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-04-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-04-03 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-04-05 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-04-07 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-04-09 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-04-11 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $otherOwnerId,
            'productId' => $productId,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-04-02 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-04-04 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $anotherProduct,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-04-02 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-04-04 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-04-02 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-04-04 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-05-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-05-03 10:00:00 Europe/Madrid'),
        ]);

        $baseUrl = sprintf(
            '/api/rents?product_id=%s&status=%s&start_date=%s&end_date=%s',
            $productId->toString(),
            RentStatus::CONFIRMED->value,
            urlencode('2025-04-01 00:00:00 Europe/Madrid'),
            urlencode('2025-04-30 23:59:59 Europe/Madrid'),
        );

        $responsePage1 = $this->request(
            'GET',
            $baseUrl . '&page=1&per_page=2',
        );

        $this->assertEquals(Response::HTTP_OK, $responsePage1->getStatusCode(), $responsePage1->getContent());
        $contentPage1 = json_decode($responsePage1->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('data', $contentPage1);
        $this->assertCount(2, $contentPage1['data']);

        $this->assertArrayHasKey('total', $contentPage1);
        $this->assertEquals(3, $contentPage1['total']);

        $responsePage2 = $this->request(
            'GET',
            $baseUrl . '&page=2&per_page=2',
        );

        $this->assertEquals(Response::HTTP_OK, $responsePage2->getStatusCode(), $responsePage2->getContent());
        $contentPage2 = json_decode($responsePage2->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('data', $contentPage2);
        $this->assertCount(1, $contentPage2['data']);

        $this->assertArrayHasKey('total', $contentPage2);
        $this->assertEquals(3, $contentPage2['total']);

        foreach ([$contentPage1, $contentPage2] as $pageContent) {
            foreach ($pageContent['data'] as $rent) {
                $this->assertSame($ownerId->toString(), $rent['owner_id']);
                $this->assertSame($productId->toString(), $rent['product_id']);
                $this->assertSame(RentStatus::CONFIRMED->value, $rent['status']);
            }
        }
    }

    public function test_admin_can_filter_rents_by_owner_id(): void
    {
        $adminId   = Uuid::v4();
        $ownerA    = Uuid::v4();
        $ownerB    = Uuid::v4();
        $productId = Uuid::v4();

        $this->givenImLoggedInAsAdminWithUserId($adminId);

        // Rents de ownerA (los que deberían salir)
        $this->givenARentWithParams([
            'ownerId'   => $ownerA,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-06-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-06-03 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $ownerA,
            'productId' => $productId,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-06-05 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-06-07 10:00:00 Europe/Madrid'),
        ]);

        // Rents de ownerB (no deberían salir con el filtro de owner_id=ownerA)
        $this->givenARentWithParams([
            'ownerId'   => $ownerB,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-06-10 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-06-12 10:00:00 Europe/Madrid'),
        ]);

        $url = sprintf(
            '/api/rents?owner_id=%s&page=1&per_page=10',
            $ownerA->toString(),
        );

        $response = $this->request('GET', $url);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('data', $content);
        $this->assertCount(2, $content['data']);

        $this->assertArrayHasKey('total', $content);
        $this->assertEquals(2, $content['total']);

        $ownerIds = array_unique(array_column($content['data'], 'owner_id'));
        $this->assertCount(1, $ownerIds);
        $this->assertSame($ownerA->toString(), $ownerIds[0]);
    }

    public function test_admin_without_owner_id_sees_own_rents(): void
    {
        $adminId   = Uuid::v4();
        $otherOwner = Uuid::v4();
        $productId = Uuid::v4();

        $this->givenImLoggedInAsAdminWithUserId($adminId);

        // Rents del propio admin como owner
        $this->givenARentWithParams([
            'ownerId'   => $adminId,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-07-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-07-03 10:00:00 Europe/Madrid'),
        ]);
        $this->givenARentWithParams([
            'ownerId'   => $adminId,
            'productId' => $productId,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-07-05 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-07-07 10:00:00 Europe/Madrid'),
        ]);

        // Rent de otro owner (no debería aparecer)
        $this->givenARentWithParams([
            'ownerId'   => $otherOwner,
            'productId' => $productId,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-07-10 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-07-12 10:00:00 Europe/Madrid'),
        ]);

        $response = $this->request(
            'GET',
            '/api/rents?page=1&per_page=10'
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('data', $content);
        $this->assertCount(2, $content['data']);

        $this->assertArrayHasKey('total', $content);
        $this->assertEquals(2, $content['total']);

        $ownerIds = array_unique(array_column($content['data'], 'owner_id'));
        $this->assertCount(1, $ownerIds);
        $this->assertSame($adminId->toString(), $ownerIds[0]);
    }
}
