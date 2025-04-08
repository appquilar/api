<?php

declare(strict_types=1);

namespace App\Product\Application\Command\RemoveSaleFeatures;

use App\Product\Application\Command\ProductCommand;
use App\Product\Application\Command\ProductCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Repository\SaleProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\Entity\Product;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: RemoveSaleFeaturesCommand::class)]
class RemoveSaleFeaturesCommandHandler extends ProductCommandHandler
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        private ProductAuthorizationServiceInterface $productAuthorizationService,
        private SaleProductRepositoryInterface $saleProductRepository,
    ) {
        parent::__construct(
            $this->productRepository,
            $this->productAuthorizationService,
        );
    }

    public function handle(RemoveSaleFeaturesCommand|ProductCommand $command, Product $product): void
    {
        if (!$product->isForSale()) {
            return; // Product is not for sale, nothing to do
        }

        $saleProduct = $this->saleProductRepository->findByProductId($product->getId());

        if ($saleProduct !== null) {
            $this->saleProductRepository->delete($saleProduct);
            $product->removeSaleProduct();
            $this->productRepository->save($product);
        }
    }
}
