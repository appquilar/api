<?php

declare(strict_types=1);

namespace App\Product\Application\Transformer;

use App\Product\Domain\Entity\Product;
use App\Shared\Application\Transformer\Transformer;
use App\Shared\Domain\Entity\Entity;
use Symfony\Component\Uid\Uuid;

class ProductTransformer implements Transformer
{
    public function transform(Product|Entity $entity): array
    {
        return [
            'id' => $entity->getId()->toString(),
            'short_id' => $entity->getShortId(),
            'name' => $entity->getName(),
            'slug' => $entity->getSlug(),
            'internal_id' => $entity->getInternalId(),
            'description' => $entity->getDescription(),
            'company_id' => $entity->getCompanyId()?->toString(),
            'category_id' => $entity->getCategoryId()?->toString(),
            'image_ids' => array_map(fn(Uuid $uuid) => $uuid->toString(), $entity->getImageIds()),
            'publication_status' => [
                'status' => $entity->getPublicationStatus()->getStatus(),
                'published_at' => $entity->getPublicationStatus()->getPublishedAt()?->format('c'),
            ],
            'deposit' => $entity->getDeposit()->toArray(),
            'tiers' => $entity->getTiers()->toArray()
        ];
    }
}