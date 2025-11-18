<?php declare(strict_types=1);

namespace App\Shared\Infrastructure\Request\Input;

use App\Shared\Domain\ValueObject\Address;
use Symfony\Component\Validator\Constraints as Assert;

final class AddressInput
{
    public function __construct(
        #[Assert\Length(max: 255, maxMessage: 'address.street.max_length')]
        public ?string $street = null,

        #[Assert\Length(max: 255, maxMessage: 'address.street2.max_length')]
        public ?string $street2 = null,

        #[Assert\Length(max: 50, maxMessage: 'address.city.max_length')]
        public ?string $city = null,

        #[Assert\Length(max: 25, maxMessage: 'address.postal_code.max_length')]
        public ?string $postalCode = null,

        #[Assert\Length(max: 50, maxMessage: 'address.state.max_length')]
        public ?string $state = null,

        #[Assert\Length(max: 50, maxMessage: 'address.country.max_length')]
        public ?string $country = null,
    ) {
    }

    public function toAddress(): Address
    {
        return new Address(
            $this->street,
            $this->street2,
            $this->city,
            $this->postalCode,
            $this->state,
            $this->country
        );
    }
}
