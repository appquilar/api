<?php declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Rent\Domain\Enum\RentOwnerType;
use App\Tests\Factories\Rent\Domain\PersistingRentFactory;
use Symfony\Component\Uid\Uuid;

trait RentContext
{
    public function givenARentWithParams(array $params): void
    {
        PersistingRentFactory::createOne($params);
    }

    public function givenItExistsARentWithAnUserAsOwnerAndAnotherUserAsRenter(Uuid $rentId, Uuid $ownerId, Uuid $renterId): void
    {
        $this->givenARentWithParams(['rentId' => $rentId, 'ownerId' => $ownerId, 'renterId' => $renterId]);
    }

    public function givenItExistsARentWithAnCompanyAsOwnerAndAnotherUserAsRenter(Uuid $rentId, Uuid $companyId, Uuid $renterId): void
    {
        $this->givenARentWithParams(['rentId' => $rentId, 'ownerId' => $companyId, 'renterId' => $renterId, 'ownerType' => RentOwnerType::COMPANY]);
    }
}
