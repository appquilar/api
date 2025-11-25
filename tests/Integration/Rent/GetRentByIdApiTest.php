<?php declare(strict_types=1);

namespace App\Tests\Integration\Rent;

use App\Rent\Domain\Enum\RentStatus;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetRentByIdApiTest extends IntegrationTestCase
{
    public function test_get_rent_by_id_returns_rent_for_owner(): void
    {
        $ownerId   = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();

        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);

        $this->givenARentWithParams([
            'rentId' => $rentId,
            'ownerId'   => $ownerId,
            'productId' => $productId,
            'status'    => RentStatus::CONFIRMED,
            'startDate' => new \DateTime('2025-01-10 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-01-15 10:00:00 Europe/Madrid'),
        ]);

        $url = sprintf('/api/rents/%s', $rentId->toString());

        $response = $this->request('GET', $url);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('data', $content);

        $this->assertSame($rentId->toString(), $content['data']['rent_id']);
        $this->assertSame($ownerId->toString(), $content['data']['owner_id']);
        $this->assertSame($productId->toString(), $content['data']['product_id']);
        $this->assertSame(RentStatus::CONFIRMED->value, $content['data']['status']);
    }

    public function test_get_rent_by_id_fails_for_invalid_uuid(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $response = $this->request(
            'GET',
            '/api/rents/invalid-uuid'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey('errors', $content['error']);
        $this->assertArrayHasKey('rentId', $content['error']['errors']);
        $this->assertArrayHasKey(0, $content['error']['errors']['rentId']);
        $this->assertEquals('rent.get.rent_id.not_blank', $content['error']['errors']['rentId'][0]);
    }

    public function test_get_rent_by_id_fails_when_blank(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $dummyId  = Uuid::v4();
        $url      = sprintf('/api/rents/%s?rent_id=', $dummyId->toString());

        $response = $this->request('GET', $url);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey('errors', $content['error']);
        $this->assertArrayHasKey('rentId', $content['error']['errors']);
        $this->assertArrayHasKey(0, $content['error']['errors']['rentId']);
        $this->assertEquals('rent.get.rent_id.not_blank', $content['error']['errors']['rentId'][0]);
    }

    public function test_get_rent_by_id_returns_404_when_not_found(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $randomId = Uuid::v4();

        $response = $this->request(
            'GET',
            sprintf('/api/rents/%s', $randomId->toString())
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey(0, $content['error']);
        $this->assertEquals(
            'Entity with id ' . $randomId->toString() . ' not found',
            $content['error'][0]
        );
    }

    public function test_get_rent_by_id_returns_401_when_rent_belongs_to_another_owner(): void
    {
        $ownerId      = Uuid::v4();
        $anotherOwner = Uuid::v4();
        $productId    = Uuid::v4();
        $rentId       = Uuid::v4();

        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenARentWithParams([
            'rentId' => $rentId,
            'ownerId'   => $anotherOwner,
            'productId' => $productId,
            'status'    => RentStatus::PENDING,
            'startDate' => new \DateTime('2025-01-01 10:00:00 Europe/Madrid'),
            'endDate'   => new \DateTime('2025-01-05 10:00:00 Europe/Madrid'),
        ]);

        $response = $this->request(
            'GET',
            sprintf('/api/rents/%s', $rentId->toString())
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey(0, $content['error']);
        $this->assertEquals('rent.user.cannot_view', $content['error'][0]);
    }
}
