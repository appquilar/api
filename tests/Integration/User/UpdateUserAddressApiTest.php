<?php declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class UpdateUserAddressApiTest extends IntegrationTestCase
{
    public function test_update_user_address_happy_path(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $payload = [
            'address' => [
                'street' => 'Fake st',
                'street2' => 'number 123',
                'city' => 'Springfield',
                'postal_code' => '1234',
                'state' => 'Iowa',
                'country' => 'US'
            ],
            'location' => [
                'latitude' => 45.78,
                'longitude' => -123.45,
            ]
        ];

        $response = $this->request(
            'PATCH',
            '/api/users/' . $userId->toString() . '/address',
            $payload,
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_try_to_update_invalid_latitude_and_longitude(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $payload = [
            'address' => [
                'street' => 'Fake st',
                'street2' => 'number 123',
                'city' => 'Springfield',
                'postal_code' => '1234',
                'state' => 'Iowa',
                'country' => 'US'
            ],
            'location' => [
                'latitude' => -91,
                'longitude' => -123.45,
            ]
        ];

        $response = $this->request(
            'PATCH',
            '/api/users/' . $userId->toString() . '/address',
            $payload,
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());


        $payload['location']['longitude'] = 185;
        $response = $this->request(
            'PATCH',
            '/api/users/' . $userId->toString() . '/address',
            $payload,
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
