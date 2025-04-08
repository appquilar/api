<?php

declare(strict_types=1);

namespace App\Product\Application\Command\RemoveRentalFeatures;

use App\Product\Application\Command\ProductCommand;
use App\Product\Application\Command\ProductCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Repository\RentalProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\Entity\Product;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: RemoveRentalFeaturesCommand::class)]
class RemoveRentalFeaturesCommandHandler extends ProductCommandHandler
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        private ProductAuthorizationServiceInterface $productAuthorizationService,
        private RentalProductRepositoryInterface $rentalProductRepository,
    ) {
        parent::__construct(
            $this->productRepository,
            $this->productAuthorizationService,
        );
    }

    public function handle(RemoveRentalFeaturesCommand|ProductCommand $command, Product $product): void
    {
        if (!$product->isForRent()) {
            return; // Product is not for rent, nothing to do
        }

        $rentalProduct = $this->rentalProductRepository->findByProductId($product->getId());

        if ($rentalProduct !== null) {
            $this->rentalProductRepository->delete($rentalProduct);
            $product->removeRentalProduct();
            $this->productRepository->save($product);
        }
    }
}
