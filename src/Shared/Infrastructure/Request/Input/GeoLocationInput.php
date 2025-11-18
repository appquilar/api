<?php declare(strict_types=1);

namespace App\Shared\Infrastructure\Request\Input;

use App\Shared\Domain\ValueObject\GeoLocation;
use Symfony\Component\Validator\Constraints as Assert;

final class GeoLocationInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Range(min: -90, max: 90)]
        public ?float $latitude = null,

        #[Assert\NotBlank]
        #[Assert\Range(min: -180, max: 180)]
        public ?float $longitude = null,

    ) {
    }

    public function toGeoLocation(): GeoLocation
    {
        return new GeoLocation($this->latitude, $this->longitude);
    }
}
