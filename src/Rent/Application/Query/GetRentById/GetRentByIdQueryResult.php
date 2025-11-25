<?php declare(strict_types=1);

namespace App\Rent\Application\Query\GetRentById;

use App\Shared\Application\Query\QueryResult;

class GetRentByIdQueryResult implements QueryResult
{
    public function __construct(
        private readonly array $rent
    ) {
    }

    public function getRent(): array
    {
        return $this->rent;
    }
}
