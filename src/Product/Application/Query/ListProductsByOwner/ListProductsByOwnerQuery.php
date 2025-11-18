<?php declare(strict_types=1);

namespace App\Product\Application\Query\ListProductsByOwner;

use App\Product\Domain\Enum\ProductOwner;
use App\Shared\Application\Query\PaginatedQuery;
use Symfony\Component\Uid\Uuid;

class ListProductsByOwnerQuery extends PaginatedQuery
{
    public function __construct(
        private Uuid $ownerId,
        private ProductOwner $ownerType,
        int $page = 1,
        int $perPage = 10,
    ) {
        parent::__construct($page, $perPage);
    }

    public function getOwnerId(): Uuid
    {
        return $this->ownerId;
    }

    public function getOwnerType(): ProductOwner
    {
        return $this->ownerType;
    }
}
