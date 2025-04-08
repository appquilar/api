<?php

declare(strict_types=1);

namespace App\Product\Application\Command\AddSaleFeatures;

use App\Product\Application\Command\ProductCommand;
use App\Product\Application\Command\ProductCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Repository\SaleProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Entity\SaleProduct;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: AddSaleFeaturesCommand::class)]
class AddSaleFeaturesCommandHandler extends ProductCommandHandler
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        private ProductAuthorizationServiceInterface $productAuthorizationService,
        private SaleProductRepositoryInterface $saleProductRepository
    ) {
        parent::__construct(
            $this->productRepository,
            $this->productAuthorizationService,
        );
    }

    public function handle(AddSaleFeaturesCommand|ProductCommand $command, Product $product): void
    {
        if ($product->isForRent()) {
            throw new BadRequestException('Cannot add sale features to a product that is for rent');
        }

        $existingSaleProduct = $this->saleProductRepository->findByProductId($product->getId());

        if ($existingSaleProduct !== null) {
            $existingSaleProduct->update(
                $command->getPrice(),
                $command->getCondition(),
                $command->getYearOfPurchase(),
                $command->isNegotiable(),
                $command->getAdditionalInformation()
            );

            $this->saleProductRepository->save($existingSaleProduct);
            return;
        }

        $saleProduct = new SaleProduct(
            $product,
            $command->getPrice(),
            $command->getCondition(),
            $command->getYearOfPurchase(),
            $command->isNegotiable(),
            $command->getAdditionalInformation()
        );

        $this->saleProductRepository->save($saleProduct);
    }
}
