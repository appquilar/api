<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Request;

use App\Shared\Infrastructure\Request\Input\MoneyInput;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateRentDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "rent.create.rent_id.not_blank"),
            new Assert\Uuid(message: "rent.create.rent_id.uuid"),
        ])]
        public ?Uuid $rentId = null,

        #[Assert\NotBlank(message: "rent.create.start_date.not_blank")]
        public ?\DateTimeImmutable $startDate = null,

        #[Assert\NotBlank(message: "rent.create.end_date.not_blank")]
        public ?\DateTimeImmutable $endDate = null,

        #[Assert\NotNull(message: "rent.create.deposit.money.not_null")]
        #[Assert\Valid]
        public ?MoneyInput $deposit = null,

        #[Assert\NotBlank(message: "rent.create.price.not_blank")]
        #[Assert\Valid]
        public ?MoneyInput $price = null,

        #[Assert\Valid]
        public ?MoneyInput $depositReturned = null,
    ) {
    }
}
