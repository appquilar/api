<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteImageDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: 'image.upload.image_id.not_blank'),
            new Assert\Uuid(message: 'image.upload.image_id.uuid')
        ])]
        public ?Uuid $imageId = null,
    ) {
    }
}
