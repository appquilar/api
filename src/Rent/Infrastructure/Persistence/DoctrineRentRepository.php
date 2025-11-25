<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Persistence;

use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentStatus;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

class DoctrineRentRepository extends DoctrineRepository implements RentRepositoryInterface
{
    public function getClass(): string
    {
        return Rent::class;
    }

    public function searchByOwner(
        Uuid $ownerId,
        ?Uuid $productId,
        ?\DateTimeInterface $startDate,
        ?\DateTimeInterface $endDate,
        ?RentStatus $status,
        int $page,
        int $perPage
    ): array {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from($this->getClass(), 'r')
            ->where('r.ownerId = :ownerId')
            ->setParameter('ownerId', $ownerId->toBinary());

        if ($productId !== null) {
            $qb
                ->andWhere('r.productId = :productId')
                ->setParameter('productId', $productId->toBinary());
        }

        if ($startDate !== null) {
            $qb
                ->andWhere('r.startDate >= :startDate')
                ->setParameter('startDate', $startDate);
        }

        if ($endDate !== null) {
            $qb
                ->andWhere('r.endDate <= :endDate')
                ->setParameter('endDate', $endDate);
        }

        if ($status !== null) {
            $qb
                ->andWhere('r.status = :status')
                ->setParameter('status', $status->value);
        }

        $countQb = clone $qb;
        $total = (int) $countQb
            ->select('COUNT(DISTINCT r.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $items = $qb
            ->orderBy('r.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }
}
