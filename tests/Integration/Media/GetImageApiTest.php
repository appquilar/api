<?php

declare(strict_types=1);

namespace App\Tests\Integration\Media;

use App\Media\Application\Enum\ImageSize;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class GetImageApiTest extends IntegrationTestCase
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

    public function testGetImageByIdApi(): void
    {
        $imageId = Uuid::v4();
        $filename = 'example.jpg';

        $this->givenIUploadedAnImage($imageId, $this->originalImagePath, $this->testStoragePath, $filename, 'jpeg');

        $this->validatePerImageSize($imageId, ImageSize::ORIGINAL);
        $this->validatePerImageSize($imageId, ImageSize::LARGE);
        $this->validatePerImageSize($imageId, ImageSize::MEDIUM);
        $this->validatePerImageSize($imageId, ImageSize::THUMBNAIL);
    }

    public function testGetNonexistentImageById(): void
    {
        $imageId = Uuid::v4();

        $response = $this->request('GET', "/api/media/images/{$imageId->toString()}/" . ImageSize::ORIGINAL->value);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    private function validatePerImageSize(Uuid $imageId, ImageSize $size): void
    {
        $response = $this->request('GET', "/api/media/images/{$imageId->toString()}/{$size->value}");

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertTrue($response->headers->has('Content-Type'));
        $this->assertStringStartsWith('image/', $response->headers->get('Content-Type'));
    }
}
