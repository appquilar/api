<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use App\Product\Domain\ValueObject\Money;
use App\Shared\Domain\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "sale_products")]
class SaleProduct extends Entity
{
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $productId;

    #[ORM\Embedded(class: Money::class, columnPrefix: false)]
    private Money $price;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $condition;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $yearOfPurchase;

    #[ORM\Column(type: "boolean")]
    private bool $negotiable;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $additionalInformation;

    public function __construct(
        Uuid $productId,
        Money $price,
        ?string $condition = null,
        ?int $yearOfPurchase = null,
        bool $negotiable = false,
        ?string $additionalInformation = null
    ) {
        parent::__construct(Uuid::v4());

        $this->productId = $productId;
        $this->price = $price;
        $this->condition = $condition;
        $this->yearOfPurchase = $yearOfPurchase;
        $this->negotiable = $negotiable;
        $this->additionalInformation = $additionalInformation;
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function getYearOfPurchase(): ?int
    {
        return $this->yearOfPurchase;
    }

    public function isNegotiable(): bool
    {
        return $this->negotiable;
    }

    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
    }

    public function update(
        Money $price,
        ?string $condition = null,
        ?int $yearOfPurchase = null,
        bool $negotiable = false,
        ?string $additionalInformation = null
    ): void {
        $this->price = $price;
        $this->condition = $condition;
        $this->yearOfPurchase = $yearOfPurchase;
        $this->negotiable = $negotiable;
        $this->additionalInformation = $additionalInformation;
    }
}