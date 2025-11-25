<?php declare(strict_types=1);

namespace App\Rent\Application\Repository;

use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentStatus;
use App\Shared\Application\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method Rent|null findById(Uuid $id)
 * @method Rent|null findOneBy(array $criteria)
 */
interface RentRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array{items: Rent[], total: int}
     */
    public function searchByOwner(
        Uuid $ownerId,
        ?Uuid $productId,
        ?\DateTimeInterface $startDate,
        ?\DateTimeInterface $endDate,
        ?RentStatus $status,
        int $page,
        int $perPage
    ): array;
}
