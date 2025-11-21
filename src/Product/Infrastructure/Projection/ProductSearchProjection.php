<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Projection;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductCategoryServiceInterface;
use App\Product\Application\Service\ProductCompanyServiceInterface;
use App\Product\Application\Service\ProductSearchIndexerInterface;
use App\Product\Application\Service\ProductUserServiceInterface;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Enum\ProductOwner;
use App\Product\Domain\ValueObject\ProductCategoryPathValueObject;
use App\Product\Infrastructure\Persistence\ProductSearchRepositoryInterface;
use App\Product\Infrastructure\ReadModel\ProductSearch\ProductSearchReadModel;
use App\Shared\Domain\ValueObject\GeoLocation;
use Symfony\Component\Uid\Uuid;

class ProductSearchProjection
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductSearchRepositoryInterface $productSearchRepository,
        private ProductCategoryServiceInterface $productCategoryService,
        private ProductUserServiceInterface $productUserService,
        private ProductCompanyServiceInterface $productCompanyService,
        private ProductSearchIndexerInterface $indexer,
    ) {
    }

    public function syncWhenProductEvent(Uuid $productId): void
    {
        $product = $this->productRepository->findById($productId);
        if ($product === null) {
            return;
        }

        $this->syncProduct($product);
    }

    private function syncProduct(Product $product): void
    {
        $location = $this->resolveLocation($product);
        $categories = $this->resolveCategories($product);
        $circle = $location?->generateCircle();
        [$ownerId, $ownerType] = $this->resolveOwner($product);

        $productSearchReadModel = $this->productSearchRepository->findById($product->getId());
        if ($productSearchReadModel === null) {
            $productSearchReadModel = new ProductSearchReadModel(
                $product->getId(),
                $product->getName(),
                $product->getSlug(),
                $product->getDescription(),
                $location?->getLatitude(),
                $location?->getLongitude(),
                $circle,
                $categories->toArray(),
                $product->getPublicationStatus()->getStatus(),
                $ownerId,
                $ownerType,
                $product->getImageIds()
            );
        } else {
            $productSearchReadModel->setName($product->getName());
            $productSearchReadModel->setDescription($product->getDescription());
            $productSearchReadModel->setSlug($product->getSlug());
            $productSearchReadModel->setLatitude($location->getLatitude());
            $productSearchReadModel->setLongitude($location->getLongitude());
            $productSearchReadModel->setCircle($circle);
            $productSearchReadModel->setCategories($categories->toArray());
            $productSearchReadModel->setPublicationStatus($product->getPublicationStatus()->getStatus());
            $productSearchReadModel->setOwnerId($ownerId);
            $productSearchReadModel->setOwnerType($ownerType);
            $productSearchReadModel->setImageIds($product->getImageIds());
        }

        $this->productSearchRepository->save($productSearchReadModel);

        $this->indexer->index(
            $productSearchReadModel->getId()->toString(),
            $productSearchReadModel->toArray()
        );
    }

    private function resolveLocation(Product $product): ?GeoLocation
    {
        return $product->belongsToUser() ?
            $this->productUserService->getUserLocationByUserId($product->getUserId()) :
            $this->productCompanyService->getCompanyLocationByCompanyId($product->getCompanyId());
    }

    private function resolveCategories(Product $product): ProductCategoryPathValueObject
    {
        if ($product->getCategoryId() === null) {
            return new ProductCategoryPathValueObject([]);
        }

        return $this->productCategoryService->getParentsFromCategory($product->getCategoryId());
    }

    public function syncWhenCategoryEvent(Uuid $categoryId): void
    {
        $categories = $this->productCategoryService->getParentsFromCategory($categoryId);
        $productsById = [];

        foreach ($categories->getItems() as $category) {
            foreach ($this->productRepository->findByCategoryId($category->getId()) as $product) {
                $productsById[(string) $product->getId()] = $product;
            }
        }

        foreach ($productsById as $product) {
            $this->syncProduct($product);
        }
    }

    private function resolveOwner(Product $product): array
    {
        return $product->belongsToUser() ?
            [$product->getUserId(), ProductOwner::USER->value] :
            [$product->getCompanyId(), ProductOwner::COMPANY->value];
    }
}
