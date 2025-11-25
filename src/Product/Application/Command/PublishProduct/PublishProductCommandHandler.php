<?php

declare(strict_types=1);

namespace App\Product\Application\Command\PublishProduct;

use App\Product\Application\Command\ProductCommand;
use App\Product\Application\Command\ProductCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Service\ProductAuthorizationServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: PublishProductCommand::class)]
class PublishProductCommandHandler extends ProductCommandHandler
{
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductAuthorizationServiceInterface $productAuthorizationService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct(
            $productRepository,
            $productAuthorizationService,
            $eventDispatcher
        );
    }

    public function handle(PublishProductCommand|ProductCommand $command, Product $product): void
    {
        $product->publish();

        $this->productRepository->save($product);

        $this->handleProductUpdateEvent($product->getId());
    }
}
