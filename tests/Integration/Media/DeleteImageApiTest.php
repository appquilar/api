<?php

declare(strict_types=1);

namespace App\Tests\Integration\Media;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class DeleteImageApiTest extends IntegrationTestCase
{
    private string $originalImagePath = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalImagePath = $this->testRootPath . '/Assets/jpeg_example.jpg';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->removeImagesDirectory($this->testStoragePath);
    }

    public function testDeleteSuccessful(): void
    {
        $imageId = Uuid::v4();
        $filename = 'example.jpg';

        $this->givenIUploadedAnImage($imageId, $this->originalImagePath, $this->testStoragePath, $filename, 'jpeg');
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());

        $response = $this->request('DELETE', '/api/media/images/' . $imageId->toString());

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteImageNotFound(): void
    {
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());

        $response = $this->request('DELETE', '/api/media/images/' . Uuid::v4()->toString());

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
