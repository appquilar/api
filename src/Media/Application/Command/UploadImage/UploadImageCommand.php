<?php

declare(strict_types=1);

namespace App\Media\Application\Command\UploadImage;

use App\Shared\Application\Command\Command;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadImageCommand implements Command
{
    public function __construct(
        private Uuid $id,
        private UploadedFile $file
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }
}
