<?php declare(strict_types=1);

namespace App\Rent\Application\Command\UpdateRent;

use App\Rent\Domain\Enum\RentStatus;
use App\Shared\Application\Command\Command;
use App\Shared\Domain\ValueObject\Money;
use Symfony\Component\Uid\Uuid;

class UpdateRentCommand implements Command
{
    public function __construct(
        private readonly Uuid        $rentId,
        private readonly ?\DateTime  $startDate = null,
        private readonly ?\DateTime  $endDate = null,
        private readonly ?Money      $deposit = null,
        private readonly ?Money      $price = null,
        private readonly ?Money      $depositReturned = null,
    ) {
    }

    public function getRentId(): Uuid
    {
        return $this->rentId;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function getDeposit(): ?Money
    {
        return $this->deposit;
    }

    public function getPrice(): ?Money
    {
        return $this->price;
    }

    public function getDepositReturned(): ?Money
    {
        return $this->depositReturned;
    }
}
