<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence;

use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Domain\Entity\CompanyUser;
use App\Company\Domain\Enum\CompanyUserRole;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

class CompanyUserRepository extends DoctrineRepository implements CompanyUserRepositoryInterface
{
    public function getClass(): string
    {
        return CompanyUser::class;
    }

    public function findCompanyIdByUserId(Uuid $userId): ?CompanyUser
    {
        return $this->findOneBy(['userId' => $userId]);
    }

    public function findPaginatedUsersByCompanyId(Uuid $companyId, int $page, int $perPage): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('cu')
            ->from(CompanyUser::class, 'cu')
            ->where('cu.companyId = :companyId')
            ->orderBy('cu.createdAt', 'DESC')
            ->setParameter('companyId', $companyId->toBinary())
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $qb->getQuery()->getResult();
    }

    public function countUsersByCompanyId(Uuid $companyId): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(cu.id)')
            ->from(CompanyUser::class, 'cu')
            ->where('cu.companyId = :companyId')
            ->setParameter('companyId', $companyId->toBinary())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
