<?php

declare(strict_types=1);

namespace App\Product\Application\Command\CreateProduct;

use App\Product\Application\Assembler\TierAssembler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Application\Service\ShortIdGeneratorInterface;
use App\Product\Application\Service\SlugForProductsManager;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Event\ProductCreated;
use App\Product\Domain\Exception\InvalidPriceConstructionException;
use App\Product\Domain\ValueObject\PublicationStatus;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CreateProductCommand::class)]
class CreateProductCommandHandler implements CommandHandler
{
    public function __construct(
        private ProductRepositoryInterface           $productRepository,
        private ProductAuthorizationServiceInterface $productAuthorizationService,
        private TierAssembler                        $tierAssembler,
        private SlugForProductsManager               $slugForProductsManager,
        private ShortIdGeneratorInterface            $shortIdGenerator,
        private EventDispatcherInterface             $eventDispatcher
    ) {
    }

    /**
     * @param CreateProductCommand|Command $command
     * @throws InvalidPriceConstructionException
     */
    public function __invoke(CreateProductCommand|Command $command): void
    {
        $shortId = $this->shortIdGenerator->generateShortId();
        $slug = $this->slugForProductsManager->generateSlugForProduct($command->getName(), $shortId);

        $product = new Product(
            $command->getProductId(),
            $command->getName(),
            $shortId,
            $slug,
            $command->getInternalId(),
            $command->getDescription(),
            $command->getQuantity(),
            $command->getDeposit()->toMoney(),
            $this->tierAssembler->createTierCollectionFromArrayOfTierInputs($command->getTiers()),
            $command->getCategoryId(),
            $command->getImageIds(),
            PublicationStatus::default(),
        );

        $this->productAuthorizationService->assignOwnership($product, $command->getCompanyId());

        $this->productRepository->save($product);

        $this->eventDispatcher->dispatch(new ProductCreated($product->getId()));
    }
}
