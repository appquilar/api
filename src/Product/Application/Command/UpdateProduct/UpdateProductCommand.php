<?php

declare(strict_types=1);

namespace App\Product\Application\Command\UpdateProduct;

use App\Shared\Application\Command\Command;
use App\Shared\Infrastructure\Request\Input\MoneyInput;
use Symfony\Component\Uid\Uuid;

class UpdateProductCommand implements Command
{
    public function __construct(
        private Uuid $productId,
        private string $name,
        private string $internalId,
        private ?string $description,
        private MoneyInput $deposit,
        private array $tiers,
        private int $quantity = 1,
        private ?Uuid $categoryId = null,
        private array $imageIds = [],
    ) {
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInternalId(): string
    {
        return $this->internalId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDeposit(): MoneyInput
    {
        return $this->deposit;
    }

    public function getTiers(): array
    {
        return $this->tiers;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCategoryId(): ?Uuid
    {
        return $this->categoryId;
    }

    public function getImageIds(): array
    {
        return $this->imageIds;
    }
}
