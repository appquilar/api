<?php declare(strict_types=1);

namespace App\Product\Application\Service;

use Symfony\Component\Uid\Uuid;

interface ProductCategoryServiceInterface
{
    /**
     * @param Uuid $categoryId
     * @return Uuid[]
     */
    public function getCategoriesTrailIds(Uuid $categoryId): array;
}
