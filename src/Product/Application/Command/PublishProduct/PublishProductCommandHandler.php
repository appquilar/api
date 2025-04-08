<?php

declare(strict_types=1);

namespace App\Product\Application\Command\PublishProduct;

use App\Product\Application\Command\ProductCommand;
use App\Product\Application\Command\ProductCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\Entity\Product;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: PublishProductCommand::class)]
class PublishProductCommandHandler extends ProductCommandHandler
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        private ProductAuthorizationServiceInterface $productAuthorizationService,
    ) {
        parent::__construct(
            $this->productRepository,
            $this->productAuthorizationService,
        );
    }

    public function handle(PublishProductCommand|ProductCommand $command, Product $product): void
    {
        // Validate that the product has either rental or sale features
        if (!$product->isForRent() && !$product->isForSale()) {
            throw new BadRequestException('Cannot publish a product without rental or sale features');
        }

        $product->publish();

        $this->productRepository->save($product);
    }
}
