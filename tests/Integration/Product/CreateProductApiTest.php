<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class CreateProductApiTest extends ProductIntegrationTestCase
{
    public function testCreateProductAsAnUserHappyPath(): void
    {
        $userId = Uuid::v4();
        $categoryId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsACategoryWithId($categoryId);

        $response = $this->request(
            'POST',
            '/api/products',
            $this->getProductRequestBody($categoryId)
        );

         $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testCreateProductForACompany(): void
    {
        $userId = Uuid::v4();
        $categoryId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenACompanyWithOwnerAndId($userId, $companyId);

        $response = $this->request(
            'POST',
            '/api/products',
            array_merge(
            $this->getProductRequestBody($categoryId),
                ['company_id' => $companyId->toString()]
            )
        );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testTryToCreateAProductForACompanyWithoutPermissions(): void
    {
        $categoryId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenACompanyWithOwnerAndId(Uuid::v4(), $companyId);

        $response = $this->request(
            'POST',
            '/api/products',
            array_merge(
                $this->getProductRequestBody($categoryId),
                ['company_id' => $companyId->toString()]
            )
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCreateProductWithTiersOverlapping(): void
    {
        $categoryId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());
        $this->givenItExistsACategoryWithId($categoryId);

        $requestBody = $this->getProductRequestBody($categoryId);
        $requestBody['tiers'] = [
            [
                'price_per_day' => [
                    'amount' => 800,
                    'currency' => 'EUR'
                ],
                'days_from' => 1,
                'days_to' => 3
            ],
            [
                'price_per_day' => [
                    'amount' => 800,
                    'currency' => 'EUR'
                ],
                'days_from' => 2,
            ],
        ];

        $response = $this->request(
            'POST',
            '/api/products',
            $requestBody
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('tiers.tier.cannot_overlap', $response->getContent(), );
    }

    public function testCreateTierWithDaysFromNegative(): void
    {
        $categoryId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());
        $this->givenItExistsACategoryWithId($categoryId);

        $requestBody = $this->getProductRequestBody($categoryId);
        $requestBody['tiers'] = [
            [
                'price_per_day' => [
                    'amount' => 800,
                    'currency' => 'EUR'
                ],
                'days_from' => -1,
                'days_to' => 3
            ],
        ];

        $response = $this->request(
            'POST',
            '/api/products',
            $requestBody
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('product.tier.money.not_null', $response->getContent(), );
    }

    public function testValidateDaysToIsBiggerThanDaysFrom(): void
    {
        $userId = Uuid::v4();
        $categoryId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsACategoryWithId($categoryId);

        $requestBody = $this->getProductRequestBody($categoryId);
        $requestBody['tiers'] = [
            [
                'price_per_day' => [
                    'amount' => 800,
                    'currency' => 'EUR'
                ],
                'days_from' => 3,
                'days_to' => 1
            ],
        ];

        $response = $this->request(
            'POST',
            '/api/products',
            $requestBody
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('tier.days_to_must_be_bigger_than_days_from', $response->getContent(), );
    }

    public function testTryToCreateAProductWithEmptyTiers(): void
    {
        $userId = Uuid::v4();
        $categoryId = Uuid::v4();
        $companyId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenACompanyWithOwnerAndId($userId, $companyId);

        $response = $this->request(
            'POST',
            '/api/products',
            array_merge(
                $this->getProductRequestBody($categoryId),
                ['company_id' => $companyId->toString(), 'tiers' => []]
            )
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testTryToCreateAProductWithSameSlugForADifferentUser(): void
    {
        $userId = Uuid::v4();
        $anotherUser = Uuid::v4();
        $categoryId = Uuid::v4();
        $slug = 'a';
        $this->givenAnUserWithIdAndEmail($anotherUser, 'a@a.com');
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsACategoryWithId($categoryId);
        $this->givenItExistsAProductWithSlugBelongingToAnUser($slug, $anotherUser);

        $response = $this->request(
            'POST',
            '/api/products',
            array_merge(
                $this->getProductRequestBody($categoryId),
                ['slug' => $slug]
            )
        );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    private function getProductRequestBody(Uuid $categoryId): array
    {
        return [
            'product_id' => Uuid::v4()->toString(),
            'name' => 'Black chair',
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