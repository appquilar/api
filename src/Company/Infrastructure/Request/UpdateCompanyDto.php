<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Request;

use App\Shared\Domain\ValueObject\PhoneNumber;
use App\Shared\Infrastructure\Request\Input\AddressInput;
use App\Shared\Infrastructure\Request\Input\GeoLocationInput;
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
        public ?string $contactEmail = null,
        ?string $phoneNumberCountryCode = null,
        ?string $phoneNumberPrefix = null,
        ?string $phoneNumberNumber = null,

        #[Assert\Valid]
        public ?AddressInput $address = null,
        #[Assert\Valid]
        public ?GeoLocationInput $location = null,
    ) {
        if ($phoneNumberCountryCode !== null && $phoneNumberPrefix !== null && $phoneNumberNumber !== null) {
            $this->phoneNumber = new PhoneNumber($phoneNumberCountryCode, $phoneNumberPrefix, $phoneNumberNumber);
        }
    }
}
