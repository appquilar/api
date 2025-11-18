<?php declare(strict_types=1);

namespace App\Product\Application\Command\MigrateOwnershipFromUserToCompany;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: MigrateOwnershipFromUserToCompanyCommand::class)]
class MigrateOwnershipFromUserToCompanyCommandHandler implements CommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function __invoke(Command|MigrateOwnershipFromUserToCompanyCommand $command): void
    {
        $products = $this->productRepository->getProductsByUserId($command->getUserId());

        foreach ($products as $product) {
            $product->changeOwnershipToCompany($command->getCompanyId());
            $this->productRepository->save($product);
        }
    }
}
