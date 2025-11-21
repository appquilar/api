<?php declare(strict_types=1);

namespace App\Product\Application\Adapter;

use App\Product\Application\Dto\ProductSearchHitDto;
use App\Product\Domain\ValueObject\PublicationStatus;
use Symfony\Component\Uid\Uuid;

interface ProductSearchAdapterInterface
{
    /**
     * @param Uuid[]|null $categoryIds
     * @return array{0:int, 1:ProductSearchHitDto[]}
     */
    public function search(
        PublicationStatus $publicationStatus,
        ?string $text = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?int $radiusKm = null,
        ?array $categoryIds = null,
        int $page = 1,
        int $perPage = 10,
    ): array;
}
