<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Service;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Domain\Dto\CategoryPathItemDto;
use App\Category\Domain\Exception\CategoryParentCircularException;
use App\Product\Application\Service\ProductCategoryServiceInterface;
use App\Product\Domain\Dto\ProductCategoryPathItemDto;
use App\Product\Domain\ValueObject\ProductCategoryPathValueObject;
use Doctrine\DBAL\Exception;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineProductCategoryService implements ProductCategoryServiceInterface
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @param Uuid $categoryId
     * @return Uuid[]
     */
    public function getCategoriesTrailIds(Uuid $categoryId): array
    {
        return $this->categoryRepository->getSubtreeIncludingSelf($categoryId, 20);
    }

    /**
     * @throws CategoryParentCircularException
     * @throws Exception
     */
    public function getParentsFromCategory(Uuid $categoryId): ProductCategoryPathValueObject
    {
        $categoryPath = $this->categoryRepository->getParentsFromCategory($categoryId);

        return new ProductCategoryPathValueObject(
            array_map(
                function (CategoryPathItemDto $item): ProductCategoryPathItemDto {
                    return new ProductCategoryPathItemDto(
                        $item->getId(),
                        $item->getSlug(),
                        $item->getName(),
                        $item->getDescription(),
                        $item->getParentId()
                    );
                },
                $categoryPath->getItems()
            )
        );
    }
}
