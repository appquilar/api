<?php declare(strict_types=1);

namespace App\Tests\Integration\Rent;

use App\Rent\Domain\Enum\RentStatus;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class UpdateRentStatusApiTest extends IntegrationTestCase
{
    public function test_update_status_being_the_owner_of_the_product(): void
    {
        $ownerId    = Uuid::v4();
        $renterId  = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();

        $this->givenAnUserWithIdAndEmail($renterId, 'a@a.com');
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $ownerId);
        $this->givenItExistsARentWithAnUserAsOwnerAndAnotherUserAsRenter($rentId, $ownerId, $renterId);

        $payload = ['rent_status' => RentStatus::PENDING->value];
        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString(). '/status', $payload);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $payload = ['rent_status' => RentStatus::CONFIRMED->value];
        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString(). '/status', $payload);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $payload = ['rent_status' => RentStatus::COMPLETED->value];
        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString(). '/status', $payload);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_try_to_update_the_rent_being_the_renter_to_a_non_cancelled_status(): void
    {
        $ownerId    = Uuid::v4();
        $renterId  = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();

        $this->givenAnUserWithIdAndEmail($ownerId, 'a@a.com');
        $this->givenImLoggedInAsRegularUserWithUserId($renterId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $ownerId);
        $this->givenItExistsARentWithAnUserAsOwnerAndAnotherUserAsRenter($rentId, $ownerId, $renterId);

        $payload = ['rent_status' => RentStatus::PENDING->value];
        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString(). '/status', $payload);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_cancel_pending_rent_being_the_renter(): void
    {
        $ownerId    = Uuid::v4();
        $renterId  = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();

        $this->givenAnUserWithIdAndEmail($renterId, 'a@a.com');
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $ownerId);
        $this->givenItExistsARentWithAnUserAsOwnerAndAnotherUserAsRenter($rentId, $ownerId, $renterId);

        $payload = ['rent_status' => RentStatus::PENDING->value];
        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString(). '/status', $payload);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $this->givenILogInWithAnAlreadyExistingUser($renterId);
        $payload = ['rent_status' => RentStatus::CANCELLED->value];
        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString(). '/status', $payload);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_try_to_update_status_an_unexistent_rent(): void
    {
        $ownerId    = Uuid::v4();
        $renterId  = Uuid::v4();
        $productId = Uuid::v4();
        $rentId    = Uuid::v4();

        $this->givenAnUserWithIdAndEmail($renterId, 'a@a.com');
        $this->givenImLoggedInAsRegularUserWithUserId($ownerId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $ownerId);

        $payload = ['rent_status' => RentStatus::PENDING->value];
        $response = $this->request('PATCH', '/api/rents/' . $rentId->toString() . '/status', $payload);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
