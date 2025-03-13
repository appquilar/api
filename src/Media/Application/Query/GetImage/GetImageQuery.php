<?php

declare(strict_types=1);

namespace App\Media\Application\Query\GetImage;

use App\Media\Application\Enum\ImageSize;
use App\Shared\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

class GetImageQuery implements Query
{
    public function __construct(
        private Uuid $imageId,
        private ImageSize $size,
    ) {
    }

    public function getImageId(): Uuid
    {
        return $this->imageId;
    }

    public function getSize(): ImageSize
    {
        return $this->size;
    }
}
