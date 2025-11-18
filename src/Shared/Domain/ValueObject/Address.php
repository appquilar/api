<?php declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class Address
{
    public function __construct(
        #[ORM\Column(type: 'text', nullable: true)]
        public string $street,
        #[ORM\Column(type: 'text', nullable: true)]
        public string $street2,
        #[ORM\Column(type: 'string', length: 50, nullable: true)]
        public string $city,
        #[ORM\Column(type: 'string', length: 25, nullable: true)]
        public string $postalCode,
        #[ORM\Column(type: 'string', length: 50, nullable: true)]
        public string $state,
        #[ORM\Column(type: 'string', length: 50, nullable: true)]
        public string $country,
    ) {
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'street2' => $this->street2,
            'city' => $this->city,
            'postal_code' => $this->postalCode,
            'state' => $this->state,
            'country' => $this->country,
        ];
    }
}
