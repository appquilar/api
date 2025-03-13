<?php

declare(strict_types=1);

namespace App\Media\Application\Command\DeleteImage;

use App\Media\Application\Event\ImageDeleted;
use App\Media\Application\Repository\ImageRepositoryInterface;
use App\Media\Application\Service\StorageServiceInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: DeleteImageCommand::class)]
class DeleteImageCommandHandler implements CommandHandler
{
    public function __construct(
        private ImageRepositoryInterface $imageRepository,
        private StorageServiceInterface $storageService,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(DeleteImageCommand|Command $command): void
    {
        $image = $this->imageRepository->findById($command->getImageId());
        if ($image === null) {
            return;
        }

        $this->storageService->delete($image->getId());
        $this->imageRepository->delete($image);

        $this->eventDispatcher->dispatch(
            new ImageDeleted($command->getImageId())
        );
    }
}
