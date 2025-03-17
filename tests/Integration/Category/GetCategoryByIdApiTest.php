<?php

declare(strict_types=1);

namespace App\Tests\Integration\Category;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetCategoryByIdApiTest extends IntegrationTestCase
{
    public function testGetCategoryById(): void
    {
        $categoryId = Uuid::v4();
        $this->givenItExistsACategoryWithId($categoryId);

        $response = $this->request('GET', '/api/categories/' . $categoryId->toString());

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetCategoryThatDoesntExist(): void
    {
        $categoryId = Uuid::v4();

        $response = $this->request('GET', '/api/categories/' . $categoryId->toString());

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
