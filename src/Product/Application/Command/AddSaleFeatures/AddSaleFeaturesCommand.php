<?php

declare(strict_types=1);

namespace App\Product\Application\Command\AddSaleFeatures;

use App\Product\Application\Command\ProductCommand;
use App\Product\Domain\ValueObject\Money;
use Symfony\Component\Uid\Uuid;

class AddSaleFeaturesCommand extends ProductCommand
{
    public function __construct(
        Uuid $productId,
        private Money $price,
        private ?string $condition = null,
        private ?int $yearOfPurchase = null,
        private bool $negotiable = false,
        private ?string $additionalInformation = null
    ) {
        parent::__construct($productId);
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
}