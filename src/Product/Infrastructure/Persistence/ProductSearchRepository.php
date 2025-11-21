<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Infrastructure\ReadModel\ProductSearch\ProductSearchReadModel;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;

class ProductSearchRepository extends DoctrineRepository implements ProductSearchRepositoryInterface
{
    public function getClass(): string
    {
        return ProductSearchReadModel::class;
    }
}
