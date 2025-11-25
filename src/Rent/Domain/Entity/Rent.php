<?php declare(strict_types=1);

namespace App\Rent\Domain\Entity;

use App\Rent\Domain\Enum\RentOwnerType;
use App\Rent\Domain\Enum\RentStatus;
use App\Rent\Domain\Exception\WrongRentStatusTransition;
use App\Shared\Domain\Entity\Entity;
use App\Shared\Domain\ValueObject\Money;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "rents")]
class Rent extends Entity
{
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $productId;

    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $ownerId;

    #[ORM\Column(type: "string", length: 20, enumType: RentOwnerType::class)]
    private RentOwnerType $ownerType;

    #[ORM\Column(type: UuidType::NAME, nullable: true)]
    private ?Uuid $renterId;

    #[ORM\Column(type: "datetime")]
    private \DateTime $startDate;

    #[ORM\Column(type: "datetime")]
    private \DateTime $endDate;

    #[ORM\Embedded(class: Money::class, columnPrefix: "deposit_")]
    private Money $deposit;

    #[ORM\Embedded(class: Money::class, columnPrefix: "price_")]
    private Money $price;

    #[ORM\Embedded(class: Money::class, columnPrefix: "deposit_returned_")]
    private ?Money $depositReturned = null;

    #[ORM\Column(type: "string", length: 20, enumType: RentStatus::class)]
    private RentStatus $status;

    public function __construct(
        Uuid $rentId,
        Uuid $productId,
        Uuid $ownerId,
        RentOwnerType $ownerType,
        ?Uuid $renterId,
        \DateTime $startDate,
        \DateTime $endDate,
        Money $deposit,
        Money $price,
        ?Money $depositReturned,
        RentStatus $status
    ) {
        parent::__construct($rentId);

        $this->productId = $productId;
        $this->ownerId = $ownerId;
        $this->ownerType = $ownerType;
        $this->renterId = $renterId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->deposit = $deposit;
        $this->price = $price;
        $this->depositReturned = $depositReturned;
        $this->status = $status;
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
    }

    public function setProductId(Uuid $productId): void
    {
        $this->productId = $productId;
    }

    public function getOwnerId(): Uuid
    {
        return $this->ownerId;
    }

    public function setOwnerId(Uuid $ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    public function getOwnerType(): RentOwnerType
    {
        return $this->ownerType;
    }

    public function setOwnerType(RentOwnerType $ownerType): void
    {
        $this->ownerType = $ownerType;
    }

    public function getRenterId(): ?Uuid
    {
        return $this->renterId;
    }

    public function setRenterId(?Uuid $renterId): void
    {
        $this->renterId = $renterId;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getDeposit(): Money
    {
        return $this->deposit;
    }

    public function setDeposit(Money $deposit): void
    {
        $this->deposit = $deposit;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function setPrice(Money $price): void
    {
        $this->price = $price;
    }

    public function getDepositReturned(): ?Money
    {
        return $this->depositReturned;
    }

    public function setDepositReturned(?Money $depositReturned): void
    {
        $this->depositReturned = $depositReturned;
    }

    public function getStatus(): RentStatus
    {
        return $this->status;
    }

    public function setStatus(RentStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @throws WrongRentStatusTransition
     */
    public function transitionTo(RentStatus $target): void
    {
        $current = $this->getStatus();

        if (!$current->canTransitionTo($target)) {
            throw new WrongRentStatusTransition(
                sprintf(
                    'rent.transition.from.to.%s.%s',
                    $this->getStatus()->value,
                    $target->value
                )
            );
        }

        $this->status = $target;
    }
}
