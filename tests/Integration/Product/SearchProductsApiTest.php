<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Product\Domain\ValueObject\PublicationStatus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class SearchProductsApiTest extends ProductIntegrationTestCase
{
    public function testSearchProductsByTextReturnsMatchingResults(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $this->givenItExistsAProductWithParams([
            'productId'         => Uuid::v4(),
            'userId'            => $userId,
            'name'              => 'Taladro Bosch Pro 4000',
            'slug'              => 'taladro-bosch-pro-4000',
            'description'       => 'Taladro muy potente de Bosch',
            'publicationStatus' => PublicationStatus::published(),
        ]);

        $this->givenItExistsAProductWithParams([
            'productId'         => Uuid::v4(),
            'userId'            => $userId,
            'name'              => 'Martillo Makita',
            'slug'              => 'martillo-makita',
            'description'       => 'Herramienta Makita',
            'publicationStatus' => PublicationStatus::published(),
        ]);

        $this->refreshOpenSearchIndex();
        $this->accessToken = null;

        $response = $this->request(
            'GET',
            '/api/products/search?text=bosch&page=1&perPage=10'
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('total', $content);
        $this->assertArrayHasKey('page', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals(1, $content['page']);

        $this->assertGreaterThanOrEqual(1, $content['total']);
        $this->assertIsArray($content['data']);

        $names = array_column($content['data'], 'name');

        $this->assertContains('Taladro Bosch Pro 4000', $names);
        $this->assertNotContains('Martillo Makita', $names);
    }

    public function testSearchProductsWithPagination(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        for ($i = 1; $i <= 15; $i++) {
            $this->givenItExistsAProductWithParams([
                'productId'         => Uuid::v4(),
                'userId'            => $userId,
                'name'              => "Producto Bosch $i",
                'slug'              => "producto-bosch-$i",
                'description'       => 'Herramienta Bosch para pruebas de paginación',
                'publicationStatus' => PublicationStatus::published(),
            ]);
        }

        $this->refreshOpenSearchIndex();
        $this->accessToken = null;

        $response = $this->request(
            'GET',
            '/api/products/search?text=Bosch&page=2&perPage=10'
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('total', $content);
        $this->assertArrayHasKey('page', $content);
        $this->assertArrayHasKey('data', $content);

        $this->assertGreaterThanOrEqual(15, $content['total']);
        $this->assertEquals(2, $content['page']);

        $this->assertIsArray($content['data']);
        $this->assertLessThanOrEqual(10, count($content['data']));
        $this->assertGreaterThanOrEqual(1, count($content['data']));
    }

    public function testSearchProductsWithoutTextReturnsOnlyPublishedProducts(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $this->givenItExistsAProductWithParams([
            'productId'         => Uuid::v4(),
            'userId'            => $userId,
            'name'              => 'Producto Publicado 1',
            'slug'              => 'producto-publicado-1',
            'description'       => 'Publicado 1',
            'publicationStatus' => PublicationStatus::published(),
        ]);

        $this->givenItExistsAProductWithParams([
            'productId'         => Uuid::v4(),
            'userId'            => $userId,
            'name'              => 'Producto Publicado 2',
            'slug'              => 'producto-publicado-2',
            'description'       => 'Publicado 2',
            'publicationStatus' => PublicationStatus::published(),
        ]);

        $this->givenItExistsAProductWithParams([
            'productId'         => Uuid::v4(),
            'userId'            => $userId,
            'name'              => 'Producto Borrador',
            'slug'              => 'producto-borrador',
            'description'       => 'No debería aparecer',
            'publicationStatus' => PublicationStatus::default(),
        ]);

        $this->refreshOpenSearchIndex();
        $this->accessToken = null;

        $response = $this->request(
            'GET',
            '/api/products/search?page=1&perPage=10'
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('total', $content);
        $this->assertArrayHasKey('page', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals(1, $content['page']);

        $this->assertIsArray($content['data']);

        $names = array_column($content['data'], 'name');

        $this->assertContains('Producto Publicado 1', $names);
        $this->assertContains('Producto Publicado 2', $names);
        $this->assertNotContains('Producto Borrador', $names);
        $this->assertEquals(2, $content['total']);
    }

    public function testSearchProductsFilteredByCategoryReturnsOnlyThatCategory(): void
    {
        $userId      = Uuid::v4();
        $categoryId1 = Uuid::v4();
        $categoryId2 = Uuid::v4();
        $parentCategoryId1 = Uuid::v4();

        $this->givenItExistsACategoryWithId($parentCategoryId1);
        $this->givenItExistsACategoryWithIdAndParentId($categoryId1, $parentCategoryId1);
        $this->givenItExistsACategoryWithId($categoryId2);

        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        $this->givenItExistsAProductWithParams([
            'productId'         => Uuid::v4(),
            'userId'            => $userId,
            'categoryId'        => $categoryId1,
            'name'              => 'Taladro Bosch Categoría 1',
            'slug'              => 'taladro-bosch-cat-1',
            'description'       => 'Herramienta en categoría 1',
            'publicationStatus' => PublicationStatus::published(),
        ]);

        $this->givenItExistsAProductWithParams([
            'productId'         => Uuid::v4(),
            'userId'            => $userId,
            'categoryId'        => $categoryId1,
            'name'              => 'Martillo Makita Categoría 1',
            'slug'              => 'martillo-makita-cat-1',
            'description'       => 'Otro producto en categoría 1',
            'publicationStatus' => PublicationStatus::published(),
        ]);

        $this->givenItExistsAProductWithParams([
            'productId'         => Uuid::v4(),
            'userId'            => $userId,
            'categoryId'        => $categoryId2,
            'name'              => 'Producto Categoría 2',
            'slug'              => 'producto-cat-2',
            'description'       => 'Producto en otra categoría',
            'publicationStatus' => PublicationStatus::published(),
        ]);

        $this->refreshOpenSearchIndex();
        $this->accessToken = null;

        $query = http_build_query([
            'categories' => [$categoryId1->toString()],
            'page'       => 1,
            'perPage'    => 10,
        ]);

        $response = $this->request(
            'GET',
            '/api/products/search?' . $query
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('total', $content);
        $this->assertArrayHasKey('page', $content);
        $this->assertArrayHasKey('data', $content);

        $this->assertEquals(1, $content['page']);
        $this->assertIsArray($content['data']);

        $names = array_column($content['data'], 'name');

        $this->assertContains('Taladro Bosch Categoría 1', $names);
        $this->assertContains('Martillo Makita Categoría 1', $names);
        $this->assertNotContains('Producto Categoría 2', $names);
        $this->assertEquals(2, $content['total']);

        $query = http_build_query([
            'categories' => [$parentCategoryId1->toString()],
            'page'       => 1,
            'perPage'    => 10,
        ]);

        $response = $this->request(
            'GET',
            '/api/products/search?' . $query
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('total', $content);
        $this->assertArrayHasKey('page', $content);
        $this->assertArrayHasKey('data', $content);

        $this->assertEquals(1, $content['page']);
        $this->assertIsArray($content['data']);

        $names = array_column($content['data'], 'name');

        $this->assertContains('Taladro Bosch Categoría 1', $names);
        $this->assertContains('Martillo Makita Categoría 1', $names);
        $this->assertNotContains('Producto Categoría 2', $names);
        $this->assertEquals(2, $content['total']);
    }

    public function testSearchProductsWithGeoFilterReturnsOkAndStructuredResponse(): void
    {
        $userId = Uuid::v4();
        $this->givenImLoggedInAsRegularUserWithUserId($userId);

        for ($i = 1; $i <= 3; $i++) {
            $this->givenItExistsAProductWithParams([
                'productId'         => Uuid::v4(),
                'userId'            => $userId,
                'name'              => "Producto Geo $i",
                'slug'              => "producto-geo-$i",
                'description'       => 'Producto con posible geolocalización',
                'publicationStatus' => PublicationStatus::published(),
            ]);
        }

        $this->refreshOpenSearchIndex();
        $this->accessToken = null;

        $response = $this->request(
            'GET',
            '/api/products/search?latitude=41.389&longitude=2.170&radius=50&page=1&perPage=10'
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('total', $content);
        $this->assertArrayHasKey('page', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertIsArray($content['data']);
    }

    public function testSearchProductsByGeoRadiusIncludesNearProductAndExcludesFarProduct(): void
    {
        $nearUserId = Uuid::v4();
        $this->givenAnUserWithIdAndSpecificLocation($nearUserId, 41.3874, 2.1686);

        $farUserId = Uuid::v4();
        $this->givenAnUserWithIdAndSpecificLocation($farUserId, 48.8566, 2.3522);

        $this->givenItExistsAProductWithParams([
            'productId'         => Uuid::v4(),
            'userId'            => $nearUserId,
            'name'              => 'Producto Cercano',
            'slug'              => 'producto-cercano',
            'description'       => 'Producto ubicado cerca del punto de búsqueda',
            'publicationStatus' => PublicationStatus::published(),
        ]);

        $this->givenItExistsAProductWithParams([
            'productId'         => Uuid::v4(),
            'userId'            => $farUserId,
            'name'              => 'Producto Lejano',
            'slug'              => 'producto-lejano',
            'description'       => 'Producto ubicado lejos del punto de búsqueda',
            'publicationStatus' => PublicationStatus::published(),
        ]);

        $this->refreshOpenSearchIndex();
        $this->accessToken = null;

        $barcelonaLat = 41.3874;
        $barcelonaLon = 2.1686;
        $mataroLat = 41.54211;
        $mataroLon = 2.4445;
        $nanterreLat = 48.8988;
        $nanterreLon = 2.1969;
        $radiusKm  = 50;

        $response = $this->request(
            'GET',
            sprintf(
                '/api/products/search?latitude=%F&longitude=%F&radius=%d&page=1&perPage=10',
                $barcelonaLat,
                $barcelonaLon,
                $radiusKm
            )
        );

        $response = $this->request(
            'GET',
            sprintf(
                '/api/products/search?latitude=%F&longitude=%F&radius=%d&page=1&perPage=10',
                $mataroLat,
                $mataroLon,
                $radiusKm
            )
        );

        $this->validateProductExistenceInResponseByRadius(
            $response,
            'Producto Cercano',
            'Producto Lejano',
            $barcelonaLat,
            $barcelonaLon,
            $radiusKm
        );
        $this->validateProductExistenceInResponseByRadius(
            $response,
            'Producto Cercano',
            'Producto Lejano',
            $mataroLat,
            $mataroLon,
            $radiusKm
        );
        $this->validateProductExistenceInResponseByRadius(
            $response,
            'Producto Lejano',
            'Producto Cercano',
            $nanterreLat,
            $nanterreLon,
            $radiusKm
        );
    }

    public function validateProductExistenceInResponseByRadius(
        Response $response,
        string $existentProductName,
        string $nonExistentProductName,
        float $latitude,
        float $longitude,
        int $radiusKm
    ): void
    {
        $response = $this->request(
            'GET',
            sprintf(
                '/api/products/search?latitude=%F&longitude=%F&radius=%d&page=1&perPage=10',
                $latitude,
                $longitude,
                $radiusKm
            )
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('total', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertIsArray($content['data']);
        $names = array_column($content['data'], 'name');
        $this->assertContains($existentProductName, $names);
        $this->assertNotContains($nonExistentProductName, $names);
    }
}
