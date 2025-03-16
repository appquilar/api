<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Request;

use App\Company\Infrastructure\Request\Constraint\UniqueOwnerId;
use App\Shared\Domain\ValueObject\PhoneNumber;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCompanyDto implements RequestDtoInterface
{
    public PhoneNumber $phoneNumber;
    public function __construct(
        #[Assert\NotBlank(message: "company.update.company_id.not_blank")]
        #[Assert\Uuid(message: "company.update.company_id.uuid")]
        public ?Uuid $companyId = null,

        #[Assert\NotBlank(message: "company.update.name.blank")]
        public string $name = '',

        #[Assert\NotBlank(message: "company.update.slug.blank")]
        public string $slug = '',

        public ?string $description = null,
        public ?string $fiscalIdentifier = null,
        public ?string $address = null,
        public ?string $postalCode = null,
        public ?string $city = null,
        public ?string $contactEmail = null,
        ?string $phoneNumberCountryCode = null,
        ?string $phoneNumberPrefix = null,
        ?string $phoneNumberNumber = null,
    ) {
        if ($phoneNumberCountryCode !== null && $phoneNumberPrefix !== null && $phoneNumberNumber !== null) {
            $this->phoneNumber = new PhoneNumber($phoneNumberCountryCode, $phoneNumberPrefix, $phoneNumberNumber);
        }
    }
}
