<?php declare(strict_types=1);

namespace App\Product\Application\Transformer;

use App\Product\Application\Dto\ProductSearchHitDto;
use App\Shared\Domain\ValueObject\GeoLocation;
use App\Shared\Infrastructure\Service\UuidV4ArrayTransformer;

class ProductHitTransformer
{
    public function transform(
        ProductSearchHitDto $product,
        ?GeoLocation $origin = null
    ): array {
        $data = [
            'id' => $product->getId()->toString(),
            'name' => $product->getName(),
            'slug' => $product->getSlug(),
            'description' => $product->getDescription(),
            'categories' => $product->getCategories()->toArray(),
            'owner_id' => $product->getOwnerId()->toString(),
            'owner_type' => $product->getOwnerType()->value,
            'location' => $product->getGeoLocation()->toArray(),
            'circle' => $product->getGeoLocation()->generateCircle(),
            'image_ids' => UuidV4ArrayTransformer::toArray($product->getImageIds())
        ];

        if ($origin !== null) {
            $data['distance'] = $product->getGeoLocation()->getDistanceInMeters($origin);
        }

        return $data;
    }
}
