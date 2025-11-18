<?php declare(strict_types=1);

namespace App\User\Application\Command\UpdateUserAddress;

use App\Shared\Application\Command\Command;
use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\GeoLocation;
use Symfony\Component\Uid\Uuid;

class UpdateUserAddressCommand implements Command
{
    public function __construct(
        private Uuid $userId,
        private ?Address $address = null,
        private ?GeoLocation $geoLocation = null,
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function getGeoLocation(): ?GeoLocation
    {
        return $this->geoLocation;
    }
}
