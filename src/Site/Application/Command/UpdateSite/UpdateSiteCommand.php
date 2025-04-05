<?php

declare(strict_types=1);

namespace App\Site\Application\Command\UpdateSite;

use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class UpdateSiteCommand implements Command
{
    /**
     * @param Uuid[] $categoryIds
     * @param Uuid[] $menuCategoryIds
     * @param Uuid[] $featuredCategoryIds
     */
    public function __construct(
        private Uuid $siteId,
        private string $name,
        private string $title,
        private string $url,
        private string $description,
        private Uuid $logoId,
        private Uuid $faviconId,
        private string $primaryColor,
        private array $categoryIds,
        private array $menuCategoryIds,
        private array $featuredCategoryIds,
    ) {
    }

    public function getSiteId(): Uuid
    {
        return $this->siteId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLogoId(): Uuid
    {
        return $this->logoId;
    }

    public function getFaviconId(): Uuid
    {
        return $this->faviconId;
    }

    public function getPrimaryColor(): string
    {
        return $this->primaryColor;
    }

    public function getCategoryIds(): array
    {
        return $this->categoryIds;
    }

    public function getMenuCategoryIds(): array
    {
        return $this->menuCategoryIds;
    }

    public function getFeaturedCategoryIds(): array
    {
        return $this->featuredCategoryIds;
    }
}
