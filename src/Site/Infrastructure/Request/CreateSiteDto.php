<?php

declare(strict_types=1);

namespace App\Site\Infrastructure\Request;

use App\Shared\Infrastructure\Request\Constraint\CategoryExists;
use App\Shared\Infrastructure\Request\Constraint\ImageExists;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSiteDto implements RequestDtoInterface
{
    /**
     * @param Uuid[]|null $categoryIds
     * @param Uuid[]|null $menuCategoryIds
     * @param Uuid[]|null $featuredCategoryIds
     */
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "site.create.site_id.not_blank"),
            new Assert\Uuid(message: "site.create.site_id.uuid"),
        ])]
        public ?Uuid $siteId = null,

        #[Assert\NotBlank(message: "site.create.name.not_blank")]
        public ?string $name = null,

        #[Assert\NotBlank(message: "site.create.title.not_blank")]
        public ?string $title = null,

        #[Assert\NotBlank(message: "site.create.url.not_blank")]
        #[Assert\Url(message: "site.create.url.url", requireTld: false)]
        public ?string $url = null,

        #[Assert\NotBlank(message: "site.create.description.not_blank")]
        public ?string $description = null,

        #[Assert\Sequentially([
            new Assert\NotBlank(message: "site.create.logo_id.not_blank"),
            new Assert\Uuid(message: "site.create.logo_id.uuid"),
            new ImageExists(message: "site.create.logo_id.exists")
        ])]
        public ?Uuid $logoId = null,

        #[Assert\Sequentially([
            new Assert\NotBlank(message: "site.create.favicon_id.not_blank"),
            new Assert\Uuid(message: "site.create.favicon_id.uuid"),
            new ImageExists(message: "site.create.favicon_id.exists")
        ])]
        public ?Uuid $faviconId = null,

        #[Assert\NotBlank(message: "site.create.primary_color.not_blank")]
        public ?string $primaryColor = null,

        #[Assert\AtLeastOneOf([
            new Assert\All([
                new Assert\Sequentially([
                    new Assert\NotBlank(message: "site.create.category_id.not_blank"),
                    new Assert\Uuid(message: "site.create.category_id.uuid"),
                    new CategoryExists(message: "site.create.category_id.exists")
                ])
            ]),
            new Assert\IsNull()
        ])]
        public ?array $categoryIds = [],

        #[Assert\AtLeastOneOf([
            new Assert\All([
                new Assert\Sequentially([
                    new Assert\NotBlank(message: "site.create.menu_category_id.not_blank"),
                    new Assert\Uuid(message: "site.create.menu_category_id.uuid"),
                    new CategoryExists(message: "site.create.menu_category_id.exists")
                ])
            ]),
            new Assert\IsNull()
        ])]
        public ?array $menuCategoryIds = [],

        #[Assert\AtLeastOneOf([
            new Assert\All([
                new Assert\Sequentially([
                    new Assert\NotBlank(message: "site.create.featured_category_id.not_blank"),
                    new Assert\Uuid(message: "site.create.featured_category_id.uuid"),
                    new CategoryExists(message: "site.create.featured_category_id.exists")
                ])
            ]),
            new Assert\IsNull()
        ])]
        public ?array $featuredCategoryIds = [],
    ) {
    }
}
