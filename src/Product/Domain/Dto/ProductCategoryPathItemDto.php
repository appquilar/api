<?php declare(strict_types=1);

namespace App\Product\Domain\Dto;

use Symfony\Component\Uid\Uuid;

class ProductCategoryPathItemDto
{
    public function __construct(
        public Uuid   $id,
        public string $slug,
        public string $name,
        public string $description,
        public ?Uuid   $parentId = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parentId,
        ];
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
