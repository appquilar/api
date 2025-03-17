<?php

declare(strict_types=1);

namespace App\Category\Domain\Entity;

use App\Shared\Domain\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "categories")]
class Category extends Entity
{
    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: UuidType::NAME, nullable: true)]
    private ?Uuid $parentId;

    #[ORM\Column(type: "string", nullable: true)]
    private ?Uuid $iconId;

    #[ORM\Column(type: "string", nullable: true)]
    private ?Uuid $featuredImageId;

    #[ORM\Column(type: "string", nullable: true)]
    private ?Uuid $landscapeImageId;

    public function __construct(
        Uuid $categoryId,
        string $name,
        ?string $description,
        string $slug,
        ?Uuid $parentId,
        ?Uuid $iconId,
        ?Uuid $featuredImageId,
        ?Uuid $landscapeImageId
    ) {
        parent::__construct($categoryId);

        $this->name = $name;
        $this->description = $description;
        $this->slug = $slug;
        $this->parentId = $parentId;
        $this->iconId = $iconId;
        $this->featuredImageId = $featuredImageId;
        $this->landscapeImageId = $landscapeImageId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getParentId(): ?Uuid
    {
        return $this->parentId;
    }

    public function getIconId(): ?Uuid
    {
        return $this->iconId;
    }

    public function getFeaturedImageId(): ?Uuid
    {
        return $this->featuredImageId;
    }

    public function getLandscapeImageId(): ?Uuid
    {
        return $this->landscapeImageId;
    }

    public function update(
        string $name,
        ?string $description,
        string $slug,
        ?Uuid $parentId,
        ?Uuid $iconId,
        ?Uuid $featuredImageId,
        ?Uuid $landscapeImageId
    ): void {
        $this->name = $name;
        $this->description = $description;
        $this->slug = $slug;
        $this->parentId = $parentId;
        $this->iconId = $iconId;
        $this->featuredImageId = $featuredImageId;
        $this->landscapeImageId = $landscapeImageId;
    }
}
