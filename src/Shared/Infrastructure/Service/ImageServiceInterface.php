<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use Symfony\Component\Uid\Uuid;

interface ImageServiceInterface
{
    public function imageExistsById(Uuid $imageId): bool;
}
