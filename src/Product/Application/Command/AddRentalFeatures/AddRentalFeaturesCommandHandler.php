<?php

declare(strict_types=1);

namespace App\Product\Application\Command\AddRentalFeatures;

use App\Product\Application\Command\ProductCommand;
use App\Product\Application\Command\ProductCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Repository\RentalProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Entity\RentalProduct;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: AddRentalFeaturesCommand::class)]
class AddRentalFeaturesCommandHandler extends ProductCommandHandler
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        private ProductAuthorizationServiceInterface $productAuthorizationService,
        private RentalProductRepositoryInterface $rentalProductRepository
    ) {
        parent::__construct(
            $this->productRepository,
            $this->productAuthorizationService,
        );
    }

    public function handle(AddRentalFeaturesCommand|ProductCommand $command, Product $product): void
    {
        if ($product->isForSale()) {
            throw new BadRequestException('Cannot add rental features to a product that is for sale');
        }

        $existingRentalProduct = $this->rentalProductRepository->findByProductId($product->getId());

        if ($existingRentalProduct !== null) {
            $existingRentalProduct->update(
                $command->getDailyPrice(),
                $command->getHourlyPrice(),
                $command->getWeeklyPrice(),
                $command->getMonthlyPrice(),
                $command->getDeposit(),
                $command->isAlwaysAvailable(),
                $command->getAvailabilityPeriods(),
                $command->includesWeekends()
            );

            $this->rentalProductRepository->save($existingRentalProduct);
            return;
        }

        $rentalProduct = new RentalProduct(
            $product,
            $command->getDailyPrice(),
            $command->getHourlyPrice(),
            $command->getWeeklyPrice(),
            $command->getMonthlyPrice(),
            $command->getDeposit(),
            $command->isAlwaysAvailable(),
            $command->getAvailabilityPeriods(),
            $command->includesWeekends()
        );

        $this->rentalProductRepository->save($rentalProduct);
    }
}
