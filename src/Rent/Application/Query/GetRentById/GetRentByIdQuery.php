<?php declare(strict_types=1);

namespace App\Rent\Application\Query\GetRentById;

use App\Shared\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

class GetRentByIdQuery implements Query
{
    public function __construct(
        private readonly Uuid $rentId
    ) {
    }

    public function getRentId(): Uuid
    {
        return $this->rentId;
    }
}
