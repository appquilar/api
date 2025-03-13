<?php

declare(strict_types=1);

namespace App\Tests\Unit\Media\Application\Command\DeleteImage;

use App\Media\Application\Command\DeleteImage\DeleteImageCommand;
use App\Media\Application\Command\DeleteImage\DeleteImageCommandHandler;
use App\Media\Application\Event\ImageDeleted;
use App\Media\Application\Repository\ImageRepositoryInterface;
use App\Media\Domain\Entity\Image;
use App\Media\Infrastructure\Service\StorageService;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

class DeleteImageCommandHandlerTest extends TestCase
{
    private ImageRepositoryInterface $imageRepositoryMock;
    private StorageService $storageServiceMock;
    private EventDispatcherInterface $eventDispatcherMock;
    private DeleteImageCommandHandler $handler;

    protected function setUp(): void
    {
        $this->imageRepositoryMock = $this->createMock(ImageRepositoryInterface::class);
        $this->storageServiceMock = $this->createMock(StorageService::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $this->handler = new DeleteImageCommandHandler($this->imageRepositoryMock, $this->storageServiceMock, $this->eventDispatcherMock);
    }

    public function testHandleDeletesImage(): void
    {
        $imageId = Uuid::v4();
        $image = new Image($imageId, 'example.jpg', 'image/jpeg',12345, 100, 200);

        $this->imageRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($imageId)
            ->willReturn($image);

        $this->storageServiceMock->expects($this->once())
            ->method('delete')
            ->with($image->getId());

        $this->imageRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($image);

        $this->eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(new ImageDeleted($imageId));

        $command = new DeleteImageCommand($imageId);
        $this->handler->__invoke($command);
    }

    public function testHandleThrowsNotFoundException(): void
    {
        $imageId = Uuid::v4();

        $this->imageRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($imageId)
            ->willReturn(null);
        $this->imageRepositoryMock->expects($this->never())
            ->method('save');

        $command = new DeleteImageCommand($imageId);
        $this->handler->__invoke($command);
    }
}
