<?php

declare(strict_types=1);

namespace App\Company\Application\Command\UpdateCompany;

use App\Shared\Application\Command\Command;
use App\Shared\Domain\ValueObject\PhoneNumber;
use Symfony\Component\Uid\Uuid;

class UpdateCompanyCommand implements Command
{
    public function __construct(
        private Uuid $companyId,
        private string $name,
        private string $slug,
        private ?string $description = null,
        private ?string $fiscalIdentifier = null,
        private ?string $address = null,
        private ?string $postalCode = null,
        private ?string $city = null,
        private ?string $contactEmail = null,
        private ?PhoneNumber $phoneNumber = null
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
