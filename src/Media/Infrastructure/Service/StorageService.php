<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Service;

use function imagecreatefrompng;
use App\Media\Application\Enum\ImageSize;
use App\Media\Application\Service\StorageServiceInterface;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class StorageService implements StorageServiceInterface
{
    public function __construct(
        private string $uploadDirectory
    ) {
        if (!is_dir($this->uploadDirectory)) {
            mkdir($this->uploadDirectory, 0755, true);
        }
    }

    public function upload(UploadedFile $file, Uuid $imageId): void
    {
        // Validate that the file is an image.
        $mimeType = $file->getMimeType();
        if (strpos($mimeType, 'image/') !== 0) {
            throw new \Exception('Invalid file type.');
        }

        $extension = $file->guessExtension() ?: 'jpg';
        $baseFileName = $imageId->toString() . '.' . $extension;
        $originalPath = $this->uploadDirectory . DIRECTORY_SEPARATOR . $baseFileName;

        // Move the uploaded file to the target directory.
        copy($file->getPathname(), $originalPath);

        // Define image sizes with semantic naming and dimensions.
        $sizes = [
            ImageSize::LARGE->value     => [800, 600],
            ImageSize::MEDIUM->value    => [400, 300],
            ImageSize::THUMBNAIL->value => [130, 85],
        ];

        foreach ($sizes as $imageSize => $dimensions) {
            list($width, $height) = $dimensions;
            $destinationPath = $this->uploadDirectory
                . DIRECTORY_SEPARATOR
                . $imageId->toString() . '_' . $imageSize . '.' . $extension;
            $this->resizeImage($originalPath, $destinationPath, $width, $height);
        }
    }

    private function resizeImage(string $sourcePath, string $destinationPath, int $targetWidth, int $targetHeight): void
    {
        $info = getimagesize($sourcePath);
        if (!$info) {
            throw new \Exception('Unable to get image info.');
        }
        $width = $info[0];
        $height = $info[1];
        $mime = $info['mime'];

        $sourceImage = match ($mime) {
            'image/jpeg' => \imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            default => throw new \Exception('Unsupported image type.'),
        };

        $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagecopyresampled(
            $resizedImage, $sourceImage,
            0, 0, 0, 0,
            $targetWidth, $targetHeight,
            $width, $height
        );

        match($mime) {
            'image/jpeg' => imagejpeg($resizedImage, $destinationPath),
            'image/png' => imagepng($resizedImage, $destinationPath),
            default => throw new \Exception('Unsupported image type.')
        };

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
    }

    public function delete(Uuid $imageId): void
    {
        $extensions = ['jpg', 'jpeg', 'png'];

        $sizes = [
            ImageSize::ORIGINAL,
            ImageSize::LARGE,
            ImageSize::MEDIUM,
            ImageSize::THUMBNAIL,
        ];

        foreach ($sizes as $size) {
            foreach ($extensions as $ext) {
                if ($size === ImageSize::ORIGINAL) {
                    $filename = sprintf('%s.%s', $imageId->toString(), $ext);
                } else {
                    $filename = sprintf('%s_%s.%s', $imageId->toString(), $size->value, $ext);
                }
                $filePath = $this->uploadDirectory . DIRECTORY_SEPARATOR . $filename;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
    }

    public function getImagePath(Uuid $imageId, ImageSize $size): string
    {
        $extensions = ['jpg', 'jpeg', 'png'];

        $suffix = $size === ImageSize::ORIGINAL ? '' : '_' . $size->value;

        foreach ($extensions as $ext) {
            $filename = $suffix === ''
                ? sprintf('%s.%s', $imageId->toString(), $ext)
                : sprintf('%s%s.%s', $imageId->toString(), $suffix, $ext);

            $filePath = $this->uploadDirectory . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($filePath)) {
                return $filePath;
            }
        }

        throw new NotFoundException('Image file not found.');
    }
}
