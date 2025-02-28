<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Application\Repository\RepositoryInterface;
use App\Shared\Domain\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Uid\Uuid;

abstract class DoctrineRepository implements RepositoryInterface
{
    protected EntityManagerInterface $entityManager;
    protected EntityRepository $repository;
    protected string $entityClass;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityClass = $this->getClass();
        $this->repository = $entityManager->getRepository($this->entityClass);
    }

    abstract public function getClass(): string;

    public function findById(Uuid $id): ?Entity
    {
        return $this->repository->find($id);
    }

    public function save(Entity $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete(Entity $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
