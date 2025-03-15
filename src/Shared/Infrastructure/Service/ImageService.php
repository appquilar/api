<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Media\Application\Repository\ImageRepositoryInterface;
use Symfony\Component\Uid\Uuid;

class ImageService implements ImageServiceInterface
{
    public function __construct(
        private ImageRepositoryInterface $imageRepository
    ) {
    }

    public function imageExistsById(Uuid $imageId): bool
    {
        return $this->imageRepository->findById($imageId) !== null;
    }
}
