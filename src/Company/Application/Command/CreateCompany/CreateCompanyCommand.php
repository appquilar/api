<?php

declare(strict_types=1);

namespace App\Company\Application\Command\CreateCompany;

use App\Shared\Application\Command\Command;
use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\GeoLocation;
use App\Shared\Domain\ValueObject\PhoneNumber;
use Symfony\Component\Uid\Uuid;

class CreateCompanyCommand implements Command
{
    public function __construct(
        private Uuid $companyId,
        private string $name,
        private Uuid $ownerId,
        private ?string $description = null,
        private ?string $fiscalIdentifier = null,
        private ?string $contactEmail = null,
        private ?PhoneNumber $phoneNumber = null,
        private ?Address $address = null,
        private ?GeoLocation $geoLocation = null,
    ) {
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOwnerId(): Uuid
    {
        return $this->ownerId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFiscalIdentifier(): ?string
    {
        return $this->fiscalIdentifier;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
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
