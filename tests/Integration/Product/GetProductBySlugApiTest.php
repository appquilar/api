<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Product\Application\Service\ShortIdGeneratorInterface;
use App\Product\Infrastructure\Service\NanoidShortIdGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetProductBySlugApiTest extends ProductIntegrationTestCase
{
    private ShortIdGeneratorInterface $shortIdGenerator;
    public function setUp(): void
    {
        parent::setUp();
        $this->shortIdGenerator = new NanoidShortIdGenerator();
    }

    public function test_get_product_by_id_happy_path(): void
    {
        $userId = Uuid::v4();
        $slug = 'test';
        $shortId = $this->shortIdGenerator->generateShortId();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsAProductWithSlugAndShortIdBelongingToAnUserAndPublished($slug, $userId, $shortId);

        $response = $this->request(
            'GET',
            '/api/products/' . $slug . '-' . $shortId,
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('slug', $slug . '-' . $shortId, $responseBody);
    }

    public function test_get_product_by_id_trying_to_view_it_being_unlogged(): void
    {
        $userId = Uuid::v4();
        $slug = 'test';
        $shortId = $this->shortIdGenerator->generateShortId();
        $this->givenAnUserWithIdAndEmail($userId, 'a@a.com');
        $this->givenItExistsAProductWithSlugAndShortIdBelongingToAnUserAndPublished($slug, $userId, $shortId);

        $response = $this->request(
            'GET',
            '/api/products/' . $slug . '-' . $shortId,
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseBody = json_decode($response->getContent(), true);
        $this->assertArrayHasKeyAndValue('slug', $slug . '-' . $shortId, $responseBody);
    }

    public function test_try_to_get_product_non_published(): void
    {
        $userId = Uuid::v4();
        $slug = 'test';
        $shortId = $this->shortIdGenerator->generateShortId();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsAProductWithSlugAndShortIdBelongingToAnUser($slug, $userId, $shortId);

        $response = $this->request(
            'GET',
            '/api/products/' . $slug . '-' . $shortId,
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_get_product_by_id_product_not_found(): void
    {
        $userId = Uuid::v4();
        $slug = 'test';
        $shortId = $this->shortIdGenerator->generateShortId();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);
        $this->givenItExistsAProductWithSlugAndShortIdBelongingToAnUser($slug, $userId, $shortId);

        $response = $this->request(
            'GET',
            '/api/products/' . $slug . '-' . $shortId,
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
