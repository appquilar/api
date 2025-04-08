<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Domain\Entity\Product;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

class ProductRepository extends DoctrineRepository implements ProductRepositoryInterface
{
    public function getClass(): string
    {
        return Product::class;
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    public function findByCompanyId(Uuid $companyId, int $page = 1, int $limit = 10): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from($this->getClass(), 'p')
            ->where('p.companyId = :companyId')
            ->setParameter('companyId', $companyId->toBinary())
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countByCompanyId(Uuid $companyId): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from($this->getClass(), 'p')
            ->where('p.companyId = :companyId')
            ->setParameter('companyId', $companyId->toBinary())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
