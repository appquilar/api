<?php

declare(strict_types=1);

namespace App\Product\Application\Command\UpdateProduct;

use App\Product\Application\Command\ProductCommand;
use Symfony\Component\Uid\Uuid;

class UpdateProductCommand extends ProductCommand
{
    /**
     * @param Uuid[] $imageIds
     */
    public function __construct(
        Uuid $productId,
        private string $name,
        private string $slug,
        private string $internalId,
        private ?string $description,
        private ?Uuid $categoryId = null,
        private array $imageIds = []
    ) {
        parent::__construct($productId);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
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
}
