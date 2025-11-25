<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Request;

use App\Rent\Domain\Enum\RentStatus;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateRentStatusDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "rent.update_status.rent_id.not_blank"),
            new Assert\Uuid(message: "rent.update_status.rent_id.uuid"),
        ])]
        public ?Uuid $rentId = null,
        #[Assert\Choice(
            callback: [RentStatus::class, 'cases'],
            message: "rent.update_status.rent_status.invalid"
        )]
        public ?RentStatus $rentStatus = null,
    ) {
    }
}
