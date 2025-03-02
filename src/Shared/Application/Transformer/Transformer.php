<?php

declare(strict_types=1);

namespace App\Shared\Application\Transformer;

use App\Shared\Domain\Entity\Entity;

interface Transformer
{
    public function transform(Entity $entity): array;
}
