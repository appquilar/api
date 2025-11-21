<?php

declare(strict_types=1);

namespace App\Tests\Unit\Media\Application\Query\GetImage;

use App\Media\Application\Enum\ImageSize;
use App\Media\Application\Query\GetImage\GetImageQuery;
use App\Media\Application\Query\GetImage\GetImageQueryHandler;
use App\Media\Application\Query\GetImage\GetImageQueryResult;
use App\Media\Application\Repository\ImageRepositoryInterface;
use App\Media\Application\Service\StorageServiceInterface;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Tests\Factories\Media\Domain\Entity\ImageFactory;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Uid\Uuid;

class GetImageQueryHandlerTest extends IntegrationTestCase
{
    private ImageRepositoryInterface $imageRepositoryMock;
    private StorageServiceInterface $storageServiceMock;
    private GetImageQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->imageRepositoryMock = $this->createMock(ImageRepositoryInterface::class);
        $this->storageServiceMock = $this->createMock(StorageServiceInterface::class);
        $this->handler = new GetImageQueryHandler(
            $this->imageRepositoryMock,
            $this->storageServiceMock
        );
    }

    public function testInvokeThrowsNotFoundExceptionWhenImageNotFound(): void
    {
        // Configure the repository to return null (image not found).
        $this->imageRepositoryMock->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        // Create a query mock.
        $query = new GetImageQuery(Uuid::v4(), ImageSize::ORIGINAL);

        // Expect a NotFoundException.
        $this->expectException(NotFoundException::class);
        $this->handler->__invoke($query);
    }

    public function testInvokeReturnsResultWhenImageFound(): void
    {
        // Create a dummy Uuid for testing.
        $imageId = Uuid::v4();
        $image = ImageFactory::createOne(['imageId' => $imageId]);
        $expectedPath = '/some/path/to/image.jpg';

        // Configure the repository to return the dummy image.
        $this->imageRepositoryMock->expects($this->once())
            ->method('findById')
            ->willReturn($image);

        // Set up an expected image path.
        $this->storageServiceMock->expects($this->once())
            ->method('getImagePath')
            ->with($imageId, ImageSize::ORIGINAL)
            ->willReturn($expectedPath);

        // Create a query mock.
        $query = new GetImageQuery($imageId, ImageSize::ORIGINAL);

        // Invoke the handler and assert the result.
        $result = $this->handler->__invoke($query);
        $this->assertInstanceOf(GetImageQueryResult::class, $result);
        $this->assertSame($expectedPath, $result->getPath());
    }
}
