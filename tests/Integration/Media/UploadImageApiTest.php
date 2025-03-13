<?php

declare(strict_types=1);

namespace App\Tests\Integration\Media;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class UploadImageApiTest extends IntegrationTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->removeImagesDirectory($this->testStoragePath);
    }

    public function testUploadJPGImage(): void
    {
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());
        $this->customHeaders = ['CONTENT_TYPE' => 'multipart/form-data'];
        $uploadedFile = new UploadedFile($this->testRootPath . '/Assets/jpeg_example.jpg', 'example.jpg', 'image/jpeg');

        $response = $this->request('POST', '/api/media/images/upload', ['imageId' => Uuid::v4()], files: ['file' => $uploadedFile]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testUploadPNGImage(): void
    {
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());
        $this->customHeaders = ['CONTENT_TYPE' => 'multipart/form-data'];
        $uploadedFile = new UploadedFile($this->testRootPath . '/Assets/png_example.png', 'example.png', 'image/png');

        $response = $this->request('POST', '/api/media/images/upload', ['imageId' => Uuid::v4()], files: ['file' => $uploadedFile]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testUploadImageMissingFile(): void
    {
        $this->givenImLoggedInAsRegularUserWithUserId(Uuid::v4());

        $this->customHeaders = ['CONTENT_TYPE' => 'multipart/form-data'];

        // We do not provide 'files' => [...]
        $response = $this->request(
            'POST',
            '/api/media/images/upload',
            ['imageId' => Uuid::v4()]
        );

        // Expect a 400 or whatever error code you return for validation issues
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        // Optionally check the returned error JSON
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success'] ?? true);
        $this->assertArrayHasKey('errors', $responseData['error'] ?? []);
        $this->assertArrayHasKey('file', $responseData['error']['errors'] ?? []);
    }
}
