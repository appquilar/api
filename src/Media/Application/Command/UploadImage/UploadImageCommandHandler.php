<?php

declare(strict_types=1);

namespace App\Media\Application\Command\UploadImage;

use App\Media\Application\Repository\ImageRepositoryInterface;
use App\Media\Application\Service\StorageServiceInterface;
use App\Media\Domain\Entity\Image;
use App\Media\Infrastructure\Service\StorageService;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UploadImageCommand::class)]
class UploadImageCommandHandler implements CommandHandler
{
    public function __construct(
        private ImageRepositoryInterface $imageRepository,
        private StorageServiceInterface $storageService
    ) {}

    public function __invoke(UploadImageCommand|Command $command): void
    {
        $file = $command->getFile();
        $this->storageService->upload($file, $command->getId());

        $info = getimagesize($file->getPathname());

        $image = new Image(
            $command->getId(),
            $file->getClientOriginalName(),
            $file->getMimeType(),
            $file->getSize(),
            is_array($info) ? $info[0] : null,
            is_array($info) ? $info[1] : null
        );

        $this->imageRepository->save($image);
    }
}
