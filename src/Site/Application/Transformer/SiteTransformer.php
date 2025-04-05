<?php

declare(strict_types=1);

namespace App\Site\Application\Transformer;

use App\Shared\Application\Transformer\Transformer;
use App\Shared\Domain\Entity\Entity;
use App\Site\Domain\Entity\Site;

class SiteTransformer implements Transformer
{
    public function transform(Site|Entity $entity): array
    {
        return [
            'site_id' => $entity->getId()->toString(),
            'name' => $entity->getName(),
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription(),
            'url' => $entity->getUrl(),
            'logo_id' => $entity->getLogoId()->toString(),
            'favicon_id' => $entity->getFaviconId()->toString(),
            'primary_color' => $entity->getPrimaryColor(),
            'category_ids' => $entity->getCategoryIds(),
            'menu_category_ids' => $entity->getMenuCategoryIds(),
            'featured_category_ids' => $entity->getFeaturedCategoryIds(),
        ];
    }
}
