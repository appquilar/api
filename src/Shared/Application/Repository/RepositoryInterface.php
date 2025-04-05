<?php

declare(strict_types=1);

namespace App\Shared\Application\Repository;

use App\Shared\Domain\Entity\Entity;
use Symfony\Component\Uid\Uuid;

interface RepositoryInterface
{
    public function findById(Uuid $id): Entity|null;
    public function findOneBy(array $params): Entity|null;
    /**
     * @return Entity[]
     */
    public function findAll(): array;
    public function save(Entity $entity): void;
    public function delete(Entity $entity): void;
}
