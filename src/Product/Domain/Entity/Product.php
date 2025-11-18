<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use App\Product\Domain\ValueObject\PublicationStatus;
use App\Product\Domain\ValueObject\TierCollection;
use App\Shared\Domain\Entity\Entity;
use App\Shared\Domain\ValueObject\Money;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "products")]
#[ORM\Index(name: "short_id_idx", columns: ["short_id"])]
#[ORM\Index(name: "internal_id_idx", columns: ["internal_id"])]
class Product extends Entity
{
    #[ORM\Column(name: "short_id", type: "string", length: 255, unique: true)]
    private string $shortId;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: "string", length: 50)]
    private string $internalId;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    #[ORM\Column(type: "integer")]
    private int $quantity = 1;

    #[ORM\Column(type: UuidType::NAME, nullable: true)]
    private ?Uuid $companyId = null;

    #[ORM\Column(type: UuidType::NAME, nullable: true)]
    private ?Uuid $userId = null;

    #[ORM\Column(type: UuidType::NAME, nullable: true)]
    private ?Uuid $categoryId;

    /** @var Uuid[] */
    #[ORM\Column(type: "uuidv4_array")]
    private array $imageIds = [];

    #[ORM\Embedded(class: PublicationStatus::class)]
    private PublicationStatus $publicationStatus;

    #[ORM\Embedded(class: Money::class, columnPrefix: "deposit_")]
    private Money $deposit;

    #[ORM\Column(name: 'tiers', type: 'tier_collection')]
    private TierCollection $tiers;

    public function __construct(
        Uuid $productId,
        string $name,
        string $shortId,
        string $slug,
        string $internalId,
        ?string $description,
        int $quantity,
        Money $deposit,
        TierCollection $tiers,
        ?Uuid $categoryId = null,
        array $imageIds = [],
        ?PublicationStatus $publicationStatus = null,
        ?Uuid $companyId = null,
        ?Uuid $userId = null,
    ) {
        parent::__construct($productId);

        $this->shortId = $shortId;
        $this->name = $name;
        $this->slug = $slug;
        $this->internalId = $internalId;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->categoryId = $categoryId;
        $this->imageIds = $imageIds;
        $this->publicationStatus = $publicationStatus ?? new PublicationStatus();
        $this->tiers = $tiers;
        $this->deposit = $deposit;
    }

    public function getShortId(): string
    {
        return $this->shortId;
    }

    public function setShortId(string $shortId): void
    {
        $this->shortId = $shortId;
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

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
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

    public function getPublicationStatus(): PublicationStatus
    {
        return $this->publicationStatus;
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

    public function getDeposit(): Money
    {
        return $this->deposit;
    }

    public function setDeposit(Money $deposit): void
    {
        $this->deposit = $deposit;
    }

    public function getTiers(): TierCollection
    {
        return $this->tiers;
    }

    public function setTiers(TierCollection $tiers): void
    {
        $this->tiers = $tiers;
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
        array $imageIds,
        Money $deposit,
        TierCollection $tiers,
        int $quantity = 1,
    ): void {
        $this->name = $name;
        $this->slug = $slug;
        $this->internalId = $internalId;
        $this->quantity = $quantity;
        $this->description = $description;
        $this->categoryId = $categoryId;
        $this->imageIds = $imageIds;
        $this->deposit = $deposit;
        $this->tiers = $tiers;
    }

    public function changeOwnershipToCompany(Uuid $companyId): void
    {
        $this->userId = null;
        $this->companyId = $companyId;
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