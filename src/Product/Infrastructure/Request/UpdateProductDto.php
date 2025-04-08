<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Request;

use App\Shared\Infrastructure\Request\Constraint\CategoryExists;
use App\Shared\Infrastructure\Request\Constraint\ImageExists;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateProductDto implements RequestDtoInterface
{
    /**
     * @param Uuid[]|null $imageIds
     */
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.update.product_id.not_blank"),
            new Assert\Uuid(message: "product.update.product_id.uuid"),
        ])]
        public ?Uuid $productId = null,

        #[Assert\NotBlank(message: "product.update.name.not_blank")]
        public ?string $name = null,

        #[Assert\NotBlank(message: "product.update.slug.not_blank")]
        public ?string $slug = null,

        #[Assert\NotBlank(message: "product.update.internal_id.not_blank")]
        public ?string $internalId = null,

        public ?string $description = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.update.category_id.not_blank"),
                new Assert\Uuid(message: "product.update.category_id.uuid"),
                new CategoryExists(message: "product.update.category_id.exists")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $categoryId = null,

        #[Assert\All([
            new Assert\Sequentially([
                new Assert\Uuid(message: "product.update.image_id.uuid"),
                new ImageExists(message: "product.update.image_id.exists")
            ])
        ])]
        public ?array $imageIds = []
    ) {
    }
}
