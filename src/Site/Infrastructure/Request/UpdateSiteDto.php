<?php

declare(strict_types=1);

namespace App\Site\Infrastructure\Request;

use App\Shared\Infrastructure\Request\Constraint\CategoryExists;
use App\Shared\Infrastructure\Request\Constraint\ImageExists;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateSiteDto implements RequestDtoInterface
{
    /**
     * @param Uuid[]|null $categoryIds
     * @param Uuid[]|null $menuCategoryIds
     * @param Uuid[]|null $featuredCategoryIds
     */
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "site.update.site_id.not_blank"),
            new Assert\Uuid(message: "site.update.site_id.uuid"),
        ])]
        public ?Uuid $siteId = null,

        #[Assert\NotBlank(message: "site.update.name.not_blank")]
        public ?string $name = null,

        #[Assert\NotBlank(message: "site.update.title.not_blank")]
        public ?string $title = null,

        #[Assert\NotBlank(message: "site.update.url.not_blank")]
        public ?string $url = null,

        #[Assert\NotBlank(message: "site.update.description.not_blank")]
        public ?string $description = null,

        #[Assert\Sequentially([
            new Assert\NotBlank(message: "site.update.logo_id.not_blank"),
            new Assert\Uuid(message: "site.update.logo_id.uuid"),
            new ImageExists(message: "site.update.logo_id.exists")
        ])]
        public ?Uuid $logoId = null,

        #[Assert\Sequentially([
            new Assert\NotBlank(message: "site.update.favicon_id.not_blank"),
            new Assert\Uuid(message: "site.update.favicon_id.uuid"),
            new ImageExists(message: "site.update.favicon_id.exists")
        ])]
        public ?Uuid $faviconId = null,

        #[Assert\NotBlank(message: "site.update.primary_color.not_blank")]
        public ?string $primaryColor = null,


        #[Assert\AtLeastOneOf([
            new Assert\All([
                new Assert\Sequentially([
                    new Assert\NotBlank(message: "site.update.category_id.not_blank"),
                    new Assert\Uuid(message: "site.update.category_id.uuid"),
                    new CategoryExists(message: "site.update.category_id.exists")
                ])
            ]),
            new Assert\IsNull()
        ])]
        public ?array $categoryIds = [],

        #[Assert\AtLeastOneOf([
            new Assert\All([
                new Assert\Sequentially([
                    new Assert\NotBlank(message: "site.update.menu_category_id.not_blank"),
                    new Assert\Uuid(message: "site.update.menu_category_id.uuid"),
                    new CategoryExists(message: "site.update.menu_category_id.exists")
                ])
            ]),
            new Assert\IsNull()
        ])]
        public ?array $menuCategoryIds = [],

        #[Assert\AtLeastOneOf([
            new Assert\All([
                new Assert\Sequentially([
                    new Assert\NotBlank(message: "site.update.featured_category_id.not_blank"),
                    new Assert\Uuid(message: "site.update.featured_category_id.uuid"),
                    new CategoryExists(message: "site.update.featured_category_id.exists")
                ])
            ]),
            new Assert\IsNull()
        ])]
        public ?array $featuredCategoryIds = [],
    ) {
    }
}
