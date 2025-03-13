<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Repository;

use App\Media\Application\Repository\ImageRepositoryInterface;
use App\Media\Domain\Entity\Image;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;

class ImageRepository extends DoctrineRepository implements ImageRepositoryInterface
{
    public function getClass(): string
    {
        return Image::class;
    }
}
