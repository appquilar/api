<?php declare(strict_types=1);

namespace App\Category\Domain\Dto;

use Symfony\Component\Uid\Uuid;

readonly class CategoryPathItemDto
{
    public function __construct(
        public Uuid    $id,
        public string  $slug,
        public string  $name,
        public string  $description,
        public int     $depth,
        public ?Uuid   $parentId = null,
        public ?string $iconId = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            Uuid::fromBinary($data['id']),
            $data['slug'],
            $data['name'],
            $data['description'],
            (int) $data['depth'],
            $data['parent_id'] !== null ? Uuid::fromBinary($data['parent_id']) : null,
            $data['icon_id']
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIconId(): ?string
    {
        return $this->iconId;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function getParentId(): ?Uuid
    {
        return $this->parentId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
