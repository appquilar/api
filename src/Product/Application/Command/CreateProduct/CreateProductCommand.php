<?php

declare(strict_types=1);

namespace App\Product\Application\Command\CreateProduct;

use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class CreateProductCommand implements Command
{
    /**
     * @param Uuid[] $imageIds
     * @param Uuid|null $companyId Provide either companyId OR userId, not both
     * @param Uuid|null $userId Provide either companyId OR userId, not both
     */
    public function __construct(
        private Uuid $productId,
        private string $name,
        private string $internalId,
        private ?string $description,
        private ?Uuid $categoryId = null,
        private array $imageIds = [],
        private ?Uuid $companyId = null,
        private ?Uuid $userId = null
    ) {
        if ($companyId === null && $userId === null) {
            throw new \InvalidArgumentException('Either companyId or userId must be provided');
        }

        if ($companyId !== null && $userId !== null) {
            throw new \InvalidArgumentException('Only one of companyId or userId must be provided, not both');
        }
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

    public function getUserId(): ?Uuid
    {
        return $this->userId;
    }

    public function belongsToCompany(): bool
    {
        return $this->companyId !== null;
    }

    public function belongsToUser(): bool
    {
        return $this->userId !== null;
    }
}
