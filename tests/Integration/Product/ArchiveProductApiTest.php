<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ArchiveProductApiTest extends IntegrationTestCase
{
    public function testPublishProductBelongingToAnUser(): void
    {
        $userId = Uuid::v4();
        $productId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $userId);

        $response = $this->request(
            'PATCH',
            '/api/products/' . $productId->toString() . '/archive'
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testPublishProductBelongingToACompany(): void
    {
        $userId = Uuid::v4();
        $productId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenACompanyWithOwnerAndId($userId, $companyId);
        $this->givenItExistsAProductWithIdBelongingToACompanyId($productId, $companyId);

        $response = $this->request(
            'PATCH',
            '/api/products/' . $productId->toString() . '/archive'
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testTryToPublishAProductBelongingToAnotherUser(): void
    {
        $userId = Uuid::v4();
        $anotherUserId = Uuid::v4();
        $productId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $anotherUserId);

        $response = $this->request(
            'PATCH',
            '/api/products/' . $productId->toString() . '/archive'
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testTryToPublishAProductBelongingToACompanyImNotPart(): void
    {
        $userId = Uuid::v4();
        $anotherUserId = Uuid::v4();
        $productId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenACompanyWithOwnerAndId($anotherUserId, $companyId);
        $this->givenItExistsAProductWithIdBelongingToACompanyId($productId, $companyId);

        $response = $this->request(
            'PATCH',
            '/api/products/' . $productId->toString() . '/archive'
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
