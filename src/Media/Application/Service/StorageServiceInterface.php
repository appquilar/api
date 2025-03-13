<?php

declare(strict_types=1);

namespace App\Media\Application\Service;

use App\Media\Application\Enum\ImageSize;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

interface StorageServiceInterface
{
    public function upload(UploadedFile $file, Uuid $imageId): void;
    public function delete(Uuid $imageId): void;
    public function getImagePath(Uuid $imageId, ImageSize $size): mixed;
}
