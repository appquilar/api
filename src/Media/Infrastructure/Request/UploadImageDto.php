<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class UploadImageDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: 'image.upload.image_id.not_blank'),
            new Assert\Uuid(message: 'image.upload.image_id.uuid')
        ])]
        public ?Uuid $imageId = null,
        #[Assert\Sequentially([
            new Assert\NotBlank(message: 'image.upload.not_blank'),
            new Assert\File(maxSize: '2M', mimeTypes: ['image/jpeg', 'image/png', 'image/jpg'])
        ])]
        public ?UploadedFile $file = null
    ) {
    }
}
