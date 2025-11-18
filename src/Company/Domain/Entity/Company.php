<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Shared\Domain\Entity\Entity;
use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\GeoLocation;
use App\Shared\Domain\ValueObject\PhoneNumber;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "companies")]
class Company extends Entity
{
    #[ORM\Column(type: "string", length: 255)]
    private string $name;
    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $slug;
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $fiscalIdentifier;
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $contactEmail;
    #[ORM\Embedded(class: PhoneNumber::class, columnPrefix: false)]
    private ?PhoneNumber $phoneNumber;
    #[ORM\Embedded(class: Address::class, columnPrefix: false)]
    private ?Address $address;
    #[ORM\Embedded(class: GeoLocation::class, columnPrefix: false)]
    private ?GeoLocation $geoLocation;

    public function __construct(
        Uuid $companyId,
        string $name,
        string $slug,
        ?string $description,
        ?string $fiscalIdentifier = null,
        ?string $contactEmail = null,
        ?PhoneNumber $phoneNumber = null,
        ?Address $address = null,
        ?GeoLocation $geoLocation = null
    ) {
        parent::__construct($companyId);

        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->fiscalIdentifier = $fiscalIdentifier;
        $this->contactEmail = $contactEmail;
        $this->phoneNumber = $phoneNumber;
        $this->address = $address;
        $this->geoLocation = $geoLocation;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
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

    public function update(
        string $name,
        string $slug,
        ?string $description,
        ?string $fiscalIdentifier = null,
        ?string $contactEmail = null,
        ?PhoneNumber $phoneNumber = null,
        ?Address $address = null,
        ?GeoLocation $geoLocation = null
    ): void
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->fiscalIdentifier = $fiscalIdentifier;
        $this->contactEmail = $contactEmail;
        $this->phoneNumber = $phoneNumber;
        $this->address = $address;
        $this->geoLocation = $geoLocation;
    }
}
