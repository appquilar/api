<?php

declare(strict_types=1);

namespace App\Media\Application\Command\DeleteImage;

use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class DeleteImageCommand implements Command
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
