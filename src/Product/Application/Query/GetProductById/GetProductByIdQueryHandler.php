<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetProductById;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Application\Transformer\ProductTransformer;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetProductByIdQuery::class)]
class GetProductByIdQueryHandler implements QueryHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductTransformer $productTransformer,
        private ProductAuthorizationServiceInterface $productAuthorizationService,
    ) {
    }

    public function __invoke(GetProductByIdQuery|Query $query): GetProductByIdQueryResult|QueryResult
    {
        $product = $this->productRepository->findById($query->getProductId());

        if ($product === null) {
            throw new EntityNotFoundException($query->getProductId());
        }

        $this->productAuthorizationService->canView($product, 'product.get_by_id.unauthorized');

        return new GetProductByIdQueryResult(
            $this->productTransformer->transform($product)
        );
    }
}
