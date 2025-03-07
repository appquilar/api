<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Shared\Domain\Entity\Entity;
use App\Shared\Domain\ValueObject\PhoneNumber;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "companies")]
#[ORM\UniqueConstraint(name: 'unique_owner_id', columns: ['owner_id'])]
class Company extends Entity
{
    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    #[ORM\Column(type: "uuid")]
    private Uuid $ownerId;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $fiscalIdentifier;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $address;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private ?string $postalCode;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $city;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $contactEmail;

    #[ORM\Embedded(class: PhoneNumber::class, columnPrefix: false)]
    private ?PhoneNumber $phoneNumber;

    public function __construct(
        Uuid $companyId,
        string $name,
        ?string $description,
        Uuid $ownerId,
        ?string $fiscalIdentifier = null,
        ?string $address = null,
        ?string $postalCode = null,
        ?string $city = null,
        ?string $contactEmail = null,
        ?PhoneNumber $phoneNumber = null
    ) {
        parent::__construct($companyId);

        $this->name = $name;
        $this->description = $description;
        $this->ownerId = $ownerId;
        $this->fiscalIdentifier = $fiscalIdentifier;
        $this->address = $address;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->contactEmail = $contactEmail;
        $this->phoneNumber = $phoneNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getOwnerId(): Uuid
    {
        return $this->ownerId;
    }

    public function getFiscalIdentifier(): ?string
    {
        return $this->fiscalIdentifier;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }
}
