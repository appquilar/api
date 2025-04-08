<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Request;

use App\Shared\Infrastructure\Request\Constraint\CategoryExists;
use App\Shared\Infrastructure\Request\Constraint\ImageExists;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateProductDto implements RequestDtoInterface
{
    /**
     * @param Uuid[]|null $imageIds
     */
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.create.product_id.not_blank"),
            new Assert\Uuid(message: "product.create.product_id.uuid"),
        ])]
        public ?Uuid $productId = null,

        #[Assert\NotBlank(message: "product.create.name.not_blank")]
        public ?string $name = null,

        #[Assert\NotBlank(message: "product.create.internal_id.not_blank")]
        public ?string $internalId = null,

        public ?string $description = null,

        #[Assert\Sequentially([
            new Assert\NotBlank(message: "product.create.company_id.not_blank"),
            new Assert\Uuid(message: "product.create.company_id.uuid"),
        ])]
        public ?Uuid $companyId = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "product.create.category_id.not_blank"),
                new Assert\Uuid(message: "product.create.category_id.uuid"),
                new CategoryExists(message: "product.create.category_id.exists")
            ]),
            new Assert\IsNull()
        ])]
        public ?Uuid $categoryId = null,

        #[Assert\All([
            new Assert\Sequentially([
                new Assert\Uuid(message: "product.create.image_id.uuid"),
                new ImageExists(message: "product.create.image_id.exists")
            ])
        ])]
        public ?array $imageIds = []
    ) {
    }
}
