<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Request;

use App\Rent\Domain\Enum\RentStatus;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class ListRentsDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Uuid(message: "rent.list.product_id.invalid", groups: ['filter'])]
        public ?Uuid $productId = null,

        #[Assert\DateTime(message: "rent.list.start_date.invalid", groups: ['filter'])]
        public ?\DateTimeInterface $startDate = null,

        #[Assert\DateTime(message: "rent.list.end_date.invalid", groups: ['filter'])]
        public ?\DateTimeInterface $endDate = null,

        #[Assert\Choice(
            callback: [RentStatus::class, 'cases'],
            message: "rent.list.status.invalid",
            groups: ['filter']
        )]
        public ?RentStatus $status = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "rent.list.owner_id.not_blank"),
                new Assert\Uuid(message: "rent.list.owner_id.uuid")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $ownerId = null,

        #[Assert\Positive(message: "pagination.page.invalid")]
        public int $page = 1,

        #[Assert\Positive(message: "pagination.per_page.invalid")]
        public int $perPage = 10,
    ) {}
}
