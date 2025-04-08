<?php

declare(strict_types=1);

namespace App\Product\Application\Command\UpdateProduct;

use App\Product\Application\Command\ProductCommand;
use App\Product\Application\Command\ProductCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\Entity\Product;
use App\Shared\Application\Service\SlugifyServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateProductCommand::class)]
class UpdateProductCommandHandler extends ProductCommandHandler
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        private ProductAuthorizationServiceInterface $productAuthorizationService,
        private SlugifyServiceInterface $slugifyService
    ) {
        parent::__construct(
            $this->productRepository,
            $this->productAuthorizationService,
        );
    }

    public function handle(UpdateProductCommand|ProductCommand $command, Product $product): void
    {
        $slug = $this->slugifyService->generate($command->getSlug());
        $this->slugifyService->validateSlugIsUnique($slug, $this->productRepository, $product->getId());

        $product->update(
            $command->getName(),
            $slug,
            $command->getInternalId(),
            $command->getDescription(),
            $command->getCategoryId(),
            $command->getImageIds()
        );

        $this->productRepository->save($product);
    }
}
