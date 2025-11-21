<?php

declare(strict_types=1);

namespace App\Product\Application\Command\ArchiveProduct;

use App\Product\Application\Command\ProductCommand;
use App\Product\Application\Command\ProductCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Application\Service\SlugForProductsManager;
use App\Product\Domain\Entity\Product;
use App\Shared\Application\Service\SlugifyServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: ArchiveProductCommand::class)]
class ArchiveProductCommandHandler extends ProductCommandHandler
{
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductAuthorizationServiceInterface $productAuthorizationService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct(
            $productRepository,
            $productAuthorizationService,
            $eventDispatcher,
        );
    }

    public function handle(ArchiveProductCommand|ProductCommand $command, Product $product): void
    {
        $product->archive();

        $this->productRepository->save($product);

        $this->handleProductUpdateEvent($product->getId());
    }
}
