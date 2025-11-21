<?php declare(strict_types=1);

namespace App\Product\Application\Command\UpdateProduct;

use App\Product\Application\Assembler\TierAssembler;
use App\Product\Application\Command\ProductCommand;
use App\Product\Application\Command\ProductCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Application\Service\SlugForProductsManager;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Exception\InvalidPriceConstructionException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateProductCommand::class)]
class UpdateProductCommandHandler extends ProductCommandHandler
{
    public function __construct(
         ProductRepositoryInterface           $productRepository,
         ProductAuthorizationServiceInterface $productAuthorizationService,
         EventDispatcherInterface             $eventDispatcher,
         private SlugForProductsManager       $slugForProductsManager,
         private TierAssembler                $tierAssembler,
    ) {
        parent::__construct(
            $productRepository,
            $productAuthorizationService,
            $eventDispatcher
        );
    }

    /**
     * @throws InvalidPriceConstructionException
     */
    public function handle(UpdateProductCommand|ProductCommand $command, Product $product): void
    {
        $product->update(
            $command->getName(),
            $this->slugForProductsManager->generateSlugForProduct(
                $command->getName(),
                $product->getShortId()
            ),
            $command->getInternalId(),
            $command->getDescription(),
            $command->getCategoryId(),
            $command->getImageIds(),
            $command->getDeposit()->toMoney(),
            $this->tierAssembler->createTierCollectionFromArrayOfTierInputs($command->getTiers()),
            $command->getQuantity()
        );

        $this->productRepository->save($product);

        $this->handleProductUpdateEvent($product->getId());
    }
}
