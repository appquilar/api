<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\ValueObject\PublicationStatus;
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

    public function paginateByCompanyId(Uuid $companyId, int $page = 1, int $limit = 10): array
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

    /**
     * @param Uuid $userId
     * @return Product[]
     */
    public function getProductsByUserId(Uuid $userId): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from($this->getClass(), 'p')
            ->where('p.userId = :userId')
            ->setParameter('userId', $userId->toBinary());

        return $qb->getQuery()->getResult();
    }

    public function paginateByUserId(Uuid $userId, int $page = 1, int $limit = 10): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from($this->getClass(), 'p')
            ->where('p.userId = :userId')
            ->setParameter('userId', $userId->toBinary())
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countByUserId(Uuid $userId): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from($this->getClass(), 'p')
            ->where('p.userId = :userId')
            ->setParameter('userId', $userId->toBinary())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByCategoryId(Uuid $categoryId): array
    {
        return $this->findBy(['categoryId' => $categoryId->toBinary()]);
    }

    public function paginateByCategoryId(array $categoriesId, int $page = 1, int $limit = 10): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->distinct()
            ->from($this->getClass(), 'p')
            ->where('p.categoryId IN (:categoriesId)')
            ->setParameter('categoriesId', array_map(fn(Uuid $categoryId) => $categoryId->toBinary(), $categoriesId))
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countByCategoryId(array $categoriesId): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNt(p.id)')
            ->distinct()
            ->from($this->getClass(), 'p')
            ->where('p.categoryId IN (:categoriesId)')
            ->setParameter('categoriesId', array_map(fn(Uuid $categoryId) => $categoryId->toBinary(), $categoriesId))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
