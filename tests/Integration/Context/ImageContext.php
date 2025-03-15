<?php

declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Media\Application\Enum\ImageSize;
use App\Tests\Factories\Media\Domain\Entity\PersistingImageFactory;
use Symfony\Component\Uid\Uuid;

trait ImageContext
{
    public string $testStoragePath = '';

    public function removeImagesDirectory(string $path): void
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("$path must be a directory");
        }

        $iterator = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($path);
    }

    public function givenIUploadedAnImage(Uuid $imageId, string $originalPath, string $targetPath, string $filename, string $extension): void
    {
        foreach(ImageSize::cases() as $size) {
            if ($size === ImageSize::ORIGINAL) {
                $imageName = sprintf('%s.%s', $imageId->toString(), $extension);
            } else {
                $imageName = sprintf('%s_%s.%s', $imageId->toString(), $size->value, $extension);
            }
            copy($originalPath, $targetPath . DIRECTORY_SEPARATOR . $imageName);
        }
        PersistingImageFactory::createOne(['imageId' => $imageId]);
    }

    public function givenItExistsAnImageWithId(Uuid $imageId): void
    {
        PersistingImageFactory::createOne(['imageId' => $imageId]);
    }
}
