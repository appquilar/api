<?php declare(strict_types=1);

namespace App\Rent\Application\Query\ListRents;

use App\Rent\Domain\Enum\RentStatus;
use App\Shared\Application\Query\PaginatedQuery;
use Symfony\Component\Uid\Uuid;

class ListRentsQuery extends PaginatedQuery
{
    public function __construct(
        private readonly ?Uuid $productId,
        private readonly ?\DateTimeInterface $startDate,
        private readonly ?\DateTimeInterface $endDate,
        private readonly ?RentStatus $status,
        private readonly ?Uuid $ownerId,
        int $page = 1,
        int $perPage = 10
    ) {
        parent::__construct($page, $perPage);
    }

    public function getProductId(): ?Uuid
    {
        return $this->productId;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function getStatus(): ?RentStatus
    {
        return $this->status;
    }

    public function getOwnerId(): ?Uuid
    {
        return $this->ownerId;
    }
}
