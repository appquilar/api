<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class PhoneNumber
{
    public function __construct(
        #[ORM\Column(type: 'string', length: 5, nullable: true)]
        private string $countryCode,
        #[ORM\Column(type: 'string', length: 5, nullable: true)]
        private string $prefix,
        #[ORM\Column(type: 'string', length: 20, nullable: true)]
        private string $number
    ) {
    }

    public function toArray(): array
    {
        return [
            'country_code' => $this->countryCode,
            'prefix' => $this->prefix,
            'number' => $this->number
        ];
    }
}
