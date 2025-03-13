<?php

declare(strict_types=1);

namespace App\Media\Application\Event;

use Symfony\Component\Uid\Uuid;

class ImageDeleted
{
    public function __construct(
        private Uuid $imageId
    ) {
    }

    public function getImageId(): Uuid
    {
        return $this->imageId;
    }
}
