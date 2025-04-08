<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetProductBySlug;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Transformer\ProductTransformer;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetProductBySlugQuery::class)]
class GetProductBySlugQueryHandler implements QueryHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductTransformer $productTransformer
    ) {
    }

    public function __invoke(GetProductBySlugQuery|Query $query): GetProductBySlugQueryResult|QueryResult
    {
        $product = $this->productRepository->findBySlug($query->getSlug());

        if ($product === null || !$product->isPublished()) {
            throw new NotFoundException('Product not found');
        }

        return new GetProductBySlugQueryResult(
            $this->productTransformer->transform($product)
        );
    }
}
