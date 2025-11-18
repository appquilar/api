<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Request;

use App\Shared\Infrastructure\Request\Constraint\CategoryExists;
use App\Shared\Infrastructure\Request\Constraint\ImageExists;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCategoryDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "category.update.category_id.not_blank"),
            new Assert\Uuid(message: "category.update.category_id.uuid"),
        ])]
        public ?Uuid $categoryId = null,

        #[Assert\NotBlank(message: "category.update.name.not_blank")]
        public ?string $name = null,

        #[Assert\NotBlank(message: "category.update.description.not_blank")]
        public ?string $description = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "category.update.parent_id.not_blank"),
                new Assert\Uuid(message: "category.update.parent_id.uuid"),
                new CategoryExists(message: "category.update.parent_id.exists")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $parentId = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "category.update.icon_id.not_blank"),
                new Assert\Uuid(message: "category.update.icon_id.uuid"),
                new ImageExists(message: "category.update.icon_id.exists")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $iconId = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "category.update.featured_image_id.not_blank"),
                new Assert\Uuid(message: "category.update.featured_image_id.uuid"),
                new ImageExists(message: "category.update.featured_image_id.exists")
            ]),
            new Assert\IsNull()
        ])]

        public ?Uuid $featuredImageId = null,
        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "category.update.landscape_image_id.not_blank"),
                new Assert\Uuid(message: "category.update.landscape_image_id.uuid"),
                new ImageExists(message: "category.update.landscape_image_id.exists")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $landscapeImageId = null
    ) {
    }
}
