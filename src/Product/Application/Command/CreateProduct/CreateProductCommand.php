<?php

declare(strict_types=1);

namespace App\Product\Application\Command\CreateProduct;

use App\Product\Infrastructure\Request\Input\TierInput;
use App\Shared\Application\Command\Command;
use App\Shared\Infrastructure\Request\Input\MoneyInput;
use Symfony\Component\Uid\Uuid;

class CreateProductCommand implements Command
{
    /**
     * @param Uuid $productId
     * @param string $name
     * @param string $internalId
     * @param string|null $description
     * @param MoneyInput $deposit
     * @param array $tiers
     * @param int $quantity
     * @param Uuid|null $categoryId
     * @param array $imageIds
     * @param Uuid|null $companyId
≤     */
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
        private ?Uuid $companyId = null,
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

    public function getCategoryId(): ?Uuid
    {
        return $this->categoryId;
    }

    public function getImageIds(): array
    {
        return $this->imageIds;
    }

    public function getCompanyId(): ?Uuid
    {
        return $this->companyId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getDeposit(): MoneyInput
    {
        return $this->deposit;
    }

    /**
     * @return TierInput[]
     */
    public function getTiers(): array
    {
        return $this->tiers;
    }
}
