<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Request;

use App\Category\Infrastructure\Request\Constraint\CategoryExists;
use App\Shared\Infrastructure\Request\Constraint\ImageExists;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCategoryDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "category.create.category_id.not_blank"),
            new Assert\Uuid(message: "category.create.category_id.uuid"),
        ])]
        public ?Uuid $categoryId = null,

        #[Assert\NotBlank(message: "category.create.name.not_blank")]
        public ?string $name = null,

        #[Assert\NotBlank(message: "category.create.description.not_blank")]
        public ?string $description = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "category.create.parent_id.not_blank"),
                new Assert\Uuid(message: "category.create.parent_id.uuid"),
                new CategoryExists(message: "category.create.parent_id.exists")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $parentId = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "category.create.icon_id.not_blank"),
                new Assert\Uuid(message: "category.create.icon_id.uuid"),
                new ImageExists(message: "category.create.icon_id.exists")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $iconId = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "category.create.featured_image_id.not_blank"),
                new Assert\Uuid(message: "category.create.featured_image_id.uuid"),
                new ImageExists(message: "category.create.featured_image_id.exists")
            ]),
            new Assert\IsNull()
        ])]

        public ?Uuid $featuredImageId = null,
        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "category.create.landscape_image_id.not_blank"),
                new Assert\Uuid(message: "category.create.landscape_image_id.uuid"),
                new ImageExists(message: "category.create.landscape_image_id.exists")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $landscapeImageId = null
    ) {
    }
}
