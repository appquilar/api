<?php

declare(strict_types=1);

namespace App\Rent\Application\Command\CreateRent;

use App\Rent\Domain\Enum\RentOwnerType;
use App\Shared\Application\Command\Command;
use App\Shared\Domain\ValueObject\Money;
use Symfony\Component\Uid\Uuid;

class CreateRentCommand implements Command
{
    public function __construct(
        private readonly Uuid $rentId,
        private readonly Uuid $productId,
        private readonly Uuid $renterId,
        private readonly \DateTimeInterface $startDate,
        private readonly \DateTimeInterface $endDate,
        private readonly Money $deposit,
        private readonly Money $price,
    ) {
    }

    public function getRentId(): Uuid
    {
        return $this->rentId;
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
    }

    public function getRenterId(): Uuid
    {
        return $this->renterId;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function getDeposit(): Money
    {
        return $this->deposit;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }
}
