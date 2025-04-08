<?php

declare(strict_types=1);

namespace App\Product\Application\Command\CreateProduct;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Service\CompanyUserServiceInterface;
use App\Product\Domain\ValueObject\PublicationStatus;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Application\Service\SlugifyServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CreateProductCommand::class)]
class CreateProductCommandHandler implements CommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private SlugifyServiceInterface $slugifyService,
        private UserGranted $userGranted,
        private CompanyUserServiceInterface $companyUserService,
    ) {
    }

    public function __invoke(CreateProductCommand|Command $command): void
    {
        $slug = $this->slugifyService->generate($command->getName());
        $this->slugifyService->validateSlugIsUnique($slug, $this->productRepository);

        $product = new Product(
            $command->getProductId(),
            $command->getName(),
            $slug,
            $command->getInternalId(),
            $command->getDescription(),
            $command->getCategoryId(),
            $command->getImageIds(),
            PublicationStatus::default()
        );

        $this->setOwnership($product);

        $this->productRepository->save($product);
    }

    private function setOwnership(Product $product): void
    {
        $user = $this->userGranted->getUser();

        if ($user === null) {
            throw new UnauthorizedException('You must be logged in to create a product');
        }

        $companyId = $this->companyUserService->getCompanyIdByUserId($user->getId());

        if ($companyId !== null) {
            $product->setCompanyId($companyId);
            return;
        }

        $product->setUserId($user->getId());
    }
}
