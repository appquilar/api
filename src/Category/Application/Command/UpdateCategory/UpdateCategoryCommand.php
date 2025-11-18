<?php

declare(strict_types=1);

namespace App\Category\Application\Command\UpdateCategory;

use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class UpdateCategoryCommand implements Command
{
    public function __construct(
        private Uuid $categoryId,
        private string $name,
        private ?string $description,
        private ?Uuid $parentId,
        private ?Uuid $icon,
        private ?Uuid $featuredImage,
        private ?Uuid $landscapeImage
    ) {
    }

    public function getCategoryId(): Uuid
    {
        return $this->categoryId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getParentId(): ?Uuid
    {
        return $this->parentId;
    }

    public function getIcon(): ?Uuid
    {
        return $this->icon;
    }

    public function getFeaturedImage(): ?Uuid
    {
        return $this->featuredImage;
    }

    public function getLandscapeImage(): ?Uuid
    {
        return $this->landscapeImage;
    }
}
