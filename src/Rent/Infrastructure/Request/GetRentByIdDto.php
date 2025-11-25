<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetRentByIdDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: "rent.get.rent_id.not_blank")]
        #[Assert\Uuid(message: "rent.get.rent_id.invalid")]
        public ?Uuid $rentId = null,
    ) {
    }
}