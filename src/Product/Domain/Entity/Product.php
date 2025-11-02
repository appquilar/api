<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use App\Product\Domain\ValueObject\PublicationStatus;
use App\Shared\Domain\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "products")]
#[ORM\Index(name: "slug_idx", columns: ["slug"])]
class Product extends Entity
{
    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: "string", length: 50)]
    private string $internalId;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    #[ORM\Column(type: UuidType::NAME, nullable: true)]
    private ?Uuid $companyId = null;

    #[ORM\Column(type: UuidType::NAME, nullable: true)]
    private ?Uuid $userId = null;

    #[ORM\Column(type: UuidType::NAME, nullable: true)]
    private ?Uuid $categoryId;

    #[ORM\OneToOne(mappedBy: "product", cascade: ["persist", "remove"])]
    private ?RentalProduct $rentalProduct = null;

    #[ORM\OneToOne(mappedBy: "product", cascade: ["persist", "remove"])]
    private ?SaleProduct $saleProduct = null;

    #[ORM\Column(type: "json")]
    private array $imageIds = [];

    #[ORM\Embedded(class: PublicationStatus::class)]
    private PublicationStatus $publicationStatus;

    /**
     * @param Uuid $productId
     * @param string $name
     * @param string $slug
     * @param string $internalId
     * @param string|null $description
     * @param Uuid|null $categoryId
     * @param array $imageIds
     * @param PublicationStatus|null $publicationStatus
     * @param Uuid|null $companyId Provide either companyId OR userId, not both
     * @param Uuid|null $userId Provide either companyId OR userId, not both
     */
    public function __construct(
        Uuid $productId,
        string $name,
        string $slug,
        string $internalId,
        ?string $description,
        ?Uuid $categoryId = null,
        array $imageIds = [],
        ?PublicationStatus $publicationStatus = null,
        ?Uuid $companyId = null,
        ?Uuid $userId = null
    ) {
        parent::__construct($productId);

        if ($companyId === null && $userId === null) {
            throw new \InvalidArgumentException('Either companyId or userId must be provided');
        }

        if ($companyId !== null && $userId !== null) {
            throw new \InvalidArgumentException('Only one of companyId or userId must be provided, not both');
        }

        $this->name = $name;
        $this->slug = $slug;
        $this->internalId = $internalId;
        $this->description = $description;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->categoryId = $categoryId;
        $this->imageIds = $imageIds;
        $this->publicationStatus = $publicationStatus ?? new PublicationStatus();
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

    public function getCompanyId(): ?Uuid
    {
        return $this->companyId;
    }

    public function setCompanyId(?Uuid $companyId): void
    {
        $this->companyId = $companyId;
    }

    public function getUserId(): ?Uuid
    {
        return $this->userId;
    }

    public function setUserId(?Uuid $userId): void
    {
        $this->userId = $userId;
    }

    public function getCategoryId(): ?Uuid
    {
        return $this->categoryId;
    }

    public function getImageIds(): array
    {
        return $this->imageIds;
    }

    public function getRentalProduct(): ?RentalProduct
    {
        return $this->rentalProduct;
    }

    public function getSaleProduct(): ?SaleProduct
    {
        return $this->saleProduct;
    }

    public function getPublicationStatus(): PublicationStatus
    {
        return $this->publicationStatus;
    }

    public function isForRent(): bool
    {
        return $this->rentalProduct !== null;
    }

    public function isForSale(): bool
    {
        return $this->saleProduct !== null;
    }

    public function isDraft(): bool
    {
        return $this->publicationStatus->isDraft();
    }

    public function isPublished(): bool
    {
        return $this->publicationStatus->isPublished();
    }

    public function isArchived(): bool
    {
        return $this->publicationStatus->isArchived();
    }

    public function belongsToCompany(): bool
    {
        return $this->companyId !== null;
    }

    public function belongsToUser(): bool
    {
        return $this->userId !== null;
    }

    public function isOwnedBy(Uuid $userId, ?Uuid $companyId = null): bool
    {
        if ($this->belongsToUser()) {
            return $this->userId->equals($userId);
        }

        if ($this->belongsToCompany() && $companyId !== null) {
            return $this->companyId->equals($companyId);
        }

        return false;
    }

    public function update(
        string $name,
        string $slug,
        string $internalId,
        ?string $description,
        ?Uuid $categoryId,
        array $imageIds
    ): void {
        $this->name = $name;
        $this->slug = $slug;
        $this->internalId = $internalId;
        $this->description = $description;
        $this->categoryId = $categoryId;
        $this->imageIds = $imageIds;
    }

    public function publish(): void
    {
        $this->publicationStatus = $this->publicationStatus->publish();
    }

    public function unpublish(): void
    {
        $this->publicationStatus = $this->publicationStatus->unpublish();
    }

    public function archive(): void
    {
        $this->publicationStatus = $this->publicationStatus->archive();
    }
}