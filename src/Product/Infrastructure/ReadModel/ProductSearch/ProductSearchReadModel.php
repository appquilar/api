<?php declare(strict_types=1);

namespace App\Product\Infrastructure\ReadModel\ProductSearch;

use App\Shared\Domain\Entity\Entity;
use App\Shared\Infrastructure\Service\UuidV4ArrayTransformer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'product_search')]
class ProductSearchReadModel extends Entity
{
    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'float', nullable: true)]
    private float $latitude;

    #[ORM\Column(type: 'float', nullable: true)]
    private float $longitude;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $circle;

    #[ORM\Column(type: 'json')]
    private array $categories;

    #[ORM\Column(type: 'string', length: 20)]
    private string $publicationStatus;

    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $ownerId;

    #[ORM\Column(type: 'string', length: 20)]
    private string $ownerType;

    /** @var Uuid[] */
    #[ORM\Column(type: "uuidv4_array")]
    private array $imageIds = [];

    public function __construct(
        Uuid $id,
        string $name,
        string $slug,
        ?string $description,
        float $latitude,
        float $longitude,
        array $circle,
        array $categories,
        string $publicationStatus,
        Uuid $ownerId,
        string $ownerType,
        array $imageIds = []
    ) {
        parent::__construct($id);
        $this->name        = $name;
        $this->slug        = $slug;
        $this->description = $description;
        $this->latitude    = $latitude;
        $this->longitude   = $longitude;
        $this->circle      = $circle;
        $this->categories  = $categories;
        $this->publicationStatus = $publicationStatus;
        $this->ownerId = $ownerId;
        $this->ownerType = $ownerType;
        $this->imageIds    = $imageIds;
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->getId()->toString(),
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'location'    => [
                'lat' => $this->latitude,
                'lon'=> $this->longitude,
            ],
            'categories'  => $this->categories,
            'publication_status'  => $this->publicationStatus,
            'owner_id'       => $this->ownerId->toString(),
            'owner_type'     => $this->ownerType,
            'image_ids'     => UuidV4ArrayTransformer::toArray($this->imageIds),
        ];
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setSlug(string $slug): void {
        $this->slug = $slug;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function setLatitude(float $latitude): void {
        $this->latitude = $latitude;
    }

    public function setLongitude(float $longitude): void {
        $this->longitude = $longitude;
    }

    public function setCircle(array $circle): void
    {
        $this->circle = $circle;
    }

    public function setCategories(array $categories): void {
        $this->categories = $categories;
    }

    public function setPublicationStatus(string $publicationStatus): void
    {
        $this->publicationStatus = $publicationStatus;
    }

    public function setOwnerId(Uuid $ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    public function setOwnerType(string $ownerType): void
    {
        $this->ownerType = $ownerType;
    }

    public function setImageIds(array $imageIds): void
    {
        $this->imageIds = $imageIds;
    }
}
