<?php declare(strict_types=1);

namespace App\Tests\Integration\Rent;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class CreateRentApiTest extends IntegrationTestCase
{
    public function test_create_rent(): void
    {
        $userId    = Uuid::v4();
        $renterId  = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();

        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $userId);

        $payload = [
            'rent_id'   => $rentId->toString(),
            'product_id' => $productId->toString(),
            'renter_id'  => $renterId->toString(),
            'start_date' => '2025-01-01 10:00:00 Europe/Madrid',
            'end_date'   => '2025-01-10 10:00:00 Europe/Madrid',
            'deposit'   => [
                'amount'   => 1000,
                'currency' => 'EUR',
            ],
            'price'     => [
                'amount'   => 500,
                'currency' => 'EUR',
            ],
        ];

        $response = $this->request('POST', '/api/rents', $payload);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function test_create_rent_unexistent_product(): void
    {
        $userId    = Uuid::v4();
        $renterId  = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();

        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $payload = [
            'rent_id'   => $rentId->toString(),
            'product_id' => $productId->toString(),
            'renter_id'  => $renterId->toString(),
            'start_date' => '2025-01-01 10:00:00 Europe/Madrid',
            'end_date'   => '2025-01-10 10:00:00 Europe/Madrid',
            'deposit'   => [
                'amount'   => 1000,
                'currency' => 'EUR',
            ],
            'price'     => [
                'amount'   => 500,
                'currency' => 'EUR',
            ],
        ];

        $response = $this->request('POST', '/api/rents', $payload);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('rent.create.product.not_found', $content['error'][0]);
    }
}
