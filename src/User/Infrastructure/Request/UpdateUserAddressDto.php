<?php declare(strict_types=1);

namespace App\User\Infrastructure\Request;

use App\Shared\Infrastructure\Request\Input\AddressInput;
use App\Shared\Infrastructure\Request\Input\GeoLocationInput;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserAddressDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: "user.update.user_id.not_blank")]
        #[Assert\Uuid(message: "user.update.user_id.uuid")]
        public ?Uuid $userId = null,
        #[Assert\Valid]
        public ?AddressInput $address = null,
        #[Assert\Valid]
        public ?GeoLocationInput $location = null,
    ) {
    }
}
