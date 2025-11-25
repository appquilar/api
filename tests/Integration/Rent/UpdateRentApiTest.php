<?php declare(strict_types=1);

namespace App\Tests\Integration\Rent;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class UpdateRentApiTest extends IntegrationTestCase
{
    public function test_update_rent_with_user_as_owner(): void
    {
        $ownerId    = Uuid::v4();
        $renterId  = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();

        $this->givenAnUserWithIdAndEmail($renterId, 'a@a.com');
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $ownerId);
        $this->givenItExistsARentWithAnUserAsOwnerAndAnotherUserAsRenter($rentId, $ownerId, $renterId);

        $payload = [
            'start_date' => '2035-01-01 10:00:00 Europe/Madrid',
            'end_date'   => '2035-01-10 10:00:00 Europe/Madrid',
            'deposit'   => [
                'amount'   => 1000,
                'currency' => 'EUR',
            ],
            'price'     => [
                'amount'   => 500,
                'currency' => 'EUR',
            ],
            'deposit_returned' => [
                'amount'   => 800,
                'currency' => 'EUR',
            ],
        ];

        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString(), $payload);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_update_rent_with_company_as_owner(): void
    {
        $ownerId    = Uuid::v4();
        $companyId  = Uuid::v4();
        $renterId  = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();

        $this->givenAnUserWithIdAndEmail($renterId, 'a@a.com');
        $this->givenACompanyWithOwnerAndId($ownerId, $companyId);
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $ownerId);
        $this->givenItExistsARentWithAnCompanyAsOwnerAndAnotherUserAsRenter($rentId, $companyId, $renterId);

        $payload = [
            'start_date' => '2035-01-01 10:00:00 Europe/Madrid',
            'end_date'   => '2035-01-10 10:00:00 Europe/Madrid',
            'deposit'   => [
                'amount'   => 1000,
                'currency' => 'EUR',
            ],
            'price'     => [
                'amount'   => 500,
                'currency' => 'EUR',
            ],
        ];

        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString(), $payload);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_try_to_update_an_unexistent_rent(): void
    {
        $ownerId    = Uuid::v4();
        $renterId  = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();

        $this->givenAnUserWithIdAndEmail($renterId, 'a@a.com');
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $ownerId);

        $payload = [
            'start_date' => '2035-01-01 10:00:00 Europe/Madrid',
            'end_date'   => '2035-01-10 10:00:00 Europe/Madrid',
            'deposit'   => [
                'amount'   => 1000,
                'currency' => 'EUR',
            ],
            'price'     => [
                'amount'   => 500,
                'currency' => 'EUR',
            ],
        ];

        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString(), $payload);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_try_to_edit_a_rent_without_permission(): void
    {
        $ownerId    = Uuid::v4();
        $renterId  = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();
        $anotherUser = Uuid::v4();

        $this->givenAnUserWithIdAndEmail($ownerId, 'a@a.com');
        $this->givenAnUserWithIdAndEmail($renterId, 'b@a.com');
        $this->givenImLoggedInAsRegularUserWithUserId($anotherUser);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $ownerId);
        $this->givenItExistsARentWithAnUserAsOwnerAndAnotherUserAsRenter($rentId, $ownerId, $renterId);

        $payload = [
            'start_date' => '2035-01-01 10:00:00 Europe/Madrid',
            'end_date'   => '2035-01-10 10:00:00 Europe/Madrid',
            'deposit'   => [
                'amount'   => 1000,
                'currency' => 'EUR',
            ],
            'price'     => [
                'amount'   => 500,
                'currency' => 'EUR',
            ],
        ];

        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString(), $payload);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
