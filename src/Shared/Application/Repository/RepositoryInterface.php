<?php

declare(strict_types=1);

namespace App\Shared\Application\Repository;

use App\Shared\Domain\Entity\Entity;

interface RepositoryInterface
{
    public function findById(string $id): ?Entity;

    public function save(Entity $entity): void;

    public function delete(Entity $entity): void;
}
