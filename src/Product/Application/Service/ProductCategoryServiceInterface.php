<?php declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Domain\ValueObject\ProductCategoryPathValueObject;
use Symfony\Component\Uid\Uuid;

interface ProductCategoryServiceInterface
{
    /**
     * @param Uuid $categoryId
     * @return Uuid[]
     */
    public function getCategoriesTrailIds(Uuid $categoryId): array;
    public function getParentsFromCategory(Uuid $categoryId): ProductCategoryPathValueObject;
}
