<?php

declare(strict_types=1);

namespace App\Product\Application\Command;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Event\ProductUpdated;
use App\Product\Domain\Service\ProductAuthorizationServiceInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

abstract class ProductCommandHandler implements CommandHandler
{
    public function __construct(
        protected ProductRepositoryInterface         $productRepository,
        private ProductAuthorizationServiceInterface $productAuthorizationService,
        private EventDispatcherInterface             $eventDispatcher
    ) {
    }

    abstract public function handle(ProductCommand $command, Product $product): void;

    public function __invoke(ProductCommand|Command $command): void
    {
        $product = $this->productRepository->findById($command->getProductId());

        if ($product === null) {
            throw new EntityNotFoundException($command->getProductId());
        }

        $this->productAuthorizationService->canEdit($product, 'product.update.unauthorized');

        $this->handle($command, $product);
    }

    protected function handleProductUpdateEvent(Uuid $productId): void
    {
        $this->eventDispatcher->dispatch(new ProductUpdated($productId));
    }
}
