<?php declare(strict_types=1);

namespace App\Rent\Application\Command\UpdateRentStatus;

use App\Rent\Domain\Enum\RentStatus;
use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class UpdateRentStatusCommand implements Command
{
    public function __construct(
        private Uuid       $rentId,
        private RentStatus $rentStatus,
    ) {
    }

    public function getRentId(): Uuid
    {
        return $this->rentId;
    }

    public function getRentStatus(): RentStatus
    {
        return $this->rentStatus;
    }
}
