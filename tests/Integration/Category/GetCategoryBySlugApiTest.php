<?php

declare(strict_types=1);

namespace App\Tests\Integration\Category;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;

class GetCategoryBySlugApiTest extends IntegrationTestCase
{
    public function testGetCategoryById(): void
    {
        $slug = 'category-slug';
        $this->givenItExistsACategoryWithSlug($slug);

        $response = $this->request('GET', '/api/categories/' . $slug);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetCategoryThatDoesntExist(): void
    {
        $slug = 'category-slug';

        $response = $this->request('GET', '/api/categories/' . $slug);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
