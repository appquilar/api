<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class UpdateProductApiTest extends ProductIntegrationTestCase
{
    public function testTryToUpdateAnUnexistentProduct(): void
    {
        $userId = Uuid::v4();
        $productId = Uuid::v4();
        $categoryId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsACategoryWithId($categoryId);

        $response = $this->request(
            'PATCH',
            '/api/products/' . $productId->toString(),
            $this->getProductRequestBody($categoryId)
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testUpdateProductHappyPath(): void
    {
        $userId = Uuid::v4();
        $productId = Uuid::v4();
        $categoryId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $userId);

        $response = $this->request(
            'PATCH',
            '/api/products/' . $productId->toString(),
            $this->getProductRequestBody($categoryId)
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testUpdateProductHappyPathBelongingToACompany(): void
    {
        $userId = Uuid::v4();
        $productId = Uuid::v4();
        $categoryId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenACompanyWithOwnerAndId($userId, $companyId);
        $this->givenItExistsAProductWithIdBelongingToACompanyId($productId, $companyId);

        $response = $this->request(
            'PATCH',
            '/api/products/' . $productId->toString(),
            array_merge(
                $this->getProductRequestBody($categoryId),
                ['company_id' => $companyId->toString()]
            )
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testTryToUpdateAProductBelongingToAnUserBeingAnotherUser(): void
    {
        $userId = Uuid::v4();
        $anotherUserId = Uuid::v4();
        $productId = Uuid::v4();
        $categoryId = Uuid::v4();
        $this->givenAnUserWithIdAndEmail($anotherUserId, 'a@a.com');
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenItExistsAProductWithIdBelongingToAnUserId($productId, $anotherUserId);

        $response = $this->request(
            'PATCH',
            '/api/products/' . $productId->toString(),
            $this->getProductRequestBody($categoryId)
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testTryToUpdateAProductBelongingToACompanyImNotPart(): void
    {
        $userId = Uuid::v4();
        $anotherUserId = Uuid::v4();
        $productId = Uuid::v4();
        $categoryId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenACompanyWithOwnerAndId($anotherUserId, $companyId);
        $this->givenItExistsAProductWithIdBelongingToACompanyId($productId, $companyId);

        $response = $this->request(
            'PATCH',
            '/api/products/' . $productId->toString(),
            array_merge(
                $this->getProductRequestBody($categoryId),
                ['company_id' => $companyId->toString()]
            )
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    private function getProductRequestBody(Uuid $categoryId): array
    {
        return [
            'name' => 'Black chair',
            'slug' => 'black-chair',
            'internal_id' => 'SKU123',
            'description' => 'A nice black chair',
            'quantity' => 3,
            'category_id' => $categoryId->toString(),
            'image_ids' => [],
            'deposit' => [
                'amount' => 1000,
                'currency' => 'EUR'
            ],
            'tiers' => [
                [
                    'price_per_day' => [
                        'amount' => 1000,
                        'currency' => 'EUR'
                    ],
                    'days_from' => 1,
                    'days_to' => 5
                ],
                [
                    'price_per_day' => [
                        'amount' => 800,
                        'currency' => 'EUR'
                    ],
                    'days_from' => 6,
                ],
            ]
        ];
    }
}