<?php

declare(strict_types=1);

namespace App\Product\Application\Transformer;

use App\Product\Domain\Entity\Product;
use App\Shared\Application\Transformer\Transformer;
use App\Shared\Domain\Entity\Entity;

class ProductTransformer implements Transformer
{
    public function __construct(
        private RentalProductTransformer $rentalProductTransformer,
        private SaleProductTransformer $saleProductTransformer
    ) {
    }

    public function transform(Product|Entity $entity): array
    {
        $data = [
            'id' => $entity->getId()->toString(),
            'name' => $entity->getName(),
            'slug' => $entity->getSlug(),
            'internal_id' => $entity->getInternalId(),
            'description' => $entity->getDescription(),
            'company_id' => $entity->getCompanyId()->toString(),
            'category_id' => $entity->getCategoryId()?->toString(),
            'image_ids' => array_map(fn($uuid) => $uuid->toString(), $entity->getImageIds()),
            'publication_status' => [
                'status' => $entity->getPublicationStatus()->getStatus(),
                'published_at' => $entity->getPublicationStatus()->getPublishedAt()?->format('c'),
            ],
            'for_rent' => $entity->isForRent(),
            'for_sale' => $entity->isForSale(),
        ];

        if ($entity->isForRent() && $entity->getRentalProduct() !== null) {
            $data['rental_features'] = $this->rentalProductTransformer->transform($entity->getRentalProduct());
        }

        if ($entity->isForSale() && $entity->getSaleProduct() !== null) {
            $data['sale_features'] = $this->saleProductTransformer->transform($entity->getSaleProduct());
        }

        return $data;
    }
}