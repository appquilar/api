<?php declare(strict_types=1);

namespace App\Product\Application\Dto;

use App\Product\Domain\Dto\ProductCategoryPathItemDto;
use App\Product\Domain\Enum\ProductOwner;
use App\Product\Domain\ValueObject\ProductCategoryPathValueObject;
use App\Product\Domain\ValueObject\PublicationStatus;
use App\Shared\Domain\ValueObject\GeoLocation;
use App\Shared\Infrastructure\Service\UuidV4ArrayTransformer;
use Symfony\Component\Uid\Uuid;

final class ProductSearchHitDto
{
    public function __construct(
        private Uuid $id,
        private string $name,
        private string $slug,
        private ?string $description,
        private GeoLocation $location,
        private ProductCategoryPathValueObject $categories,
        private PublicationStatus $publicationStatus,
        private Uuid $ownerId,
        private ProductOwner $ownerType,
        private array $imageIds = [],
    ) {
    }

    public static function fromArray(array $productSearchHitData): self
    {
        return new self(
            Uuid::fromString($productSearchHitData['id']),
            $productSearchHitData['name'],
            $productSearchHitData['slug'],
            $productSearchHitData['description'],
            new GeoLocation(
                $productSearchHitData['location']['lat'],
                $productSearchHitData['location']['lon'],
            ),
            ProductCategoryPathValueObject::fromArray($productSearchHitData['categories']),
            new PublicationStatus($productSearchHitData['publication_status']),
            Uuid::fromString($productSearchHitData['owner_id']),
            ProductOwner::from($productSearchHitData['owner_type']),
            UuidV4ArrayTransformer::fromArray($productSearchHitData['image_ids'])
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getGeoLocation(): GeoLocation
    {
        return $this->location;
    }

    public function getCategories(): ProductCategoryPathValueObject
    {
        return $this->categories;
    }

    public function getPublicationStatus(): PublicationStatus
    {
        return $this->publicationStatus;
    }

    public function getOwnerId(): Uuid
    {
        return $this->ownerId;
    }

    public function getOwnerType(): ProductOwner
    {
        return $this->ownerType;
    }

    public function getImageIds(): array
    {
        return $this->imageIds;
    }
}
