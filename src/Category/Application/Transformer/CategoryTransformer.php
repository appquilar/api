<?php

declare(strict_types=1);

namespace App\Category\Application\Transformer;

use App\Category\Domain\Entity\Category;
use App\Shared\Application\Transformer\Transformer;
use App\Shared\Domain\Entity\Entity;

class CategoryTransformer implements Transformer
{
    public function transform(Category|Entity $entity): array
    {
        return [
            'id' => $entity->getId()->toString(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'slug' => $entity->getSlug(),
            'parent_id' => $entity->getParentId()?->toString(),
            'icon_id' => $entity->getIconId()?->toString(),
            'featured_image_id' => $entity->getFeaturedImageId()?->toString(),
            'landscape_image_id' => $entity->getLandscapeImageId()?->toString(),
        ];
    }
}
