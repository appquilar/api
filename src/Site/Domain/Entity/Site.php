<?php

declare(strict_types=1);

namespace App\Site\Domain\Entity;

use App\Shared\Domain\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "sites")]
#[ORM\Index(name: "name_idx", columns: ["name"])]
#[ORM\Index(name: "url_idx", columns: ["url"])]
class Site extends Entity
{
    #[ORM\Column(type: "string")]
    private string $name;

    #[ORM\Column(type: "string")]
    private string $title;

    #[ORM\Column(type: "string")]
    private string $url;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $logoId;

    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $faviconId;

    #[ORM\Column(type: "string", length: 25)]
    private string $primaryColor;

    #[ORM\Column(type: "json")]
    private array $categoryIds = [];

    #[ORM\Column(type: "json")]
    private array $menuCategoryIds = [];

    #[ORM\Column(type: "json")]
    private array $featuredCategoryIds = [];

    /**
     * @param Uuid[] $categoryIds
     * @param Uuid[] $menuCategoryIds
     * @param Uuid[] $featuredCategoryIds
     */
    public function __construct(
        Uuid $siteId,
        string $name,
        string $title,
        string $url,
        string $description,
        Uuid $logoId,
        Uuid $faviconId,
        string $primaryColor,
        array $categoryIds,
        array $menuCategoryIds,
        array $featuredCategoryIds
    ) {
        parent::__construct($siteId);

        $this->name = $name;
        $this->title = $title;
        $this->url = $url;
        $this->description = $description;
        $this->logoId = $logoId;
        $this->faviconId = $faviconId;
        $this->primaryColor = $primaryColor;
        $this->categoryIds = $categoryIds;
        $this->menuCategoryIds = $menuCategoryIds;
        $this->featuredCategoryIds = $featuredCategoryIds;
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

    /**
     * @param Uuid[] $categoryIds
     * @param Uuid[] $menuCategoryIds
     * @param Uuid[] $featuredCategoryIds
     */
    public function update(
        string $name,
        string $title,
        string $url,
        string $description,
        Uuid $logoId,
        Uuid $faviconId,
        string $primaryColor,
        array $categoryIds,
        array $menuCategoryIds,
        array $featuredCategoryIds
    ): void
    {
        $this->name = $name;
        $this->title = $title;
        $this->url = $url;
        $this->description = $description;
        $this->logoId = $logoId;
        $this->faviconId = $faviconId;
        $this->primaryColor = $primaryColor;
        $this->categoryIds = $categoryIds;
        $this->menuCategoryIds = $menuCategoryIds;
        $this->featuredCategoryIds = $featuredCategoryIds;
    }
}
