<?php

declare(strict_types=1);

namespace App\Product\Application\Command;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\Entity\Product;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;

abstract class ProductCommandHandler implements CommandHandler
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        private ProductAuthorizationServiceInterface $productAuthorizationService,
    ) {
    }

    abstract public function handle(ProductCommand $command, Product $product): void;

    public function __invoke(ProductCommand|Command $command): void
    {
        $product = $this->productRepository->findById($command->getProductId());

        if ($product === null) {
            throw new EntityNotFoundException($command->getProductId());
        }

        $this->authorizeEdit($product);

        $this->handle($command, $product);
    }

    protected function authorizeEdit(
        Product $product,
    ): void
    {
        if (!$this->productAuthorizationService->canEdit($product)) {
            throw new UnauthorizedException('You do not have permission to edit this product');
        }
    }
}
