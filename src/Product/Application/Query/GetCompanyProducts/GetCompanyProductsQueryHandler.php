<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetCompanyProducts;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Application\Transformer\ProductTransformer;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetCompanyProductsQuery::class)]
class GetCompanyProductsQueryHandler implements QueryHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductTransformer $productTransformer,
    ) {
    }

    public function __invoke(GetCompanyProductsQuery|Query $query): GetCompanyProductsQueryResult|QueryResult
    {
        $products = $this->productRepository->findByCompanyId(
            $query->getCompanyId(),
            $query->getPage(),
            $query->getPerPage()
        );

        $total = $this->productRepository->countByCompanyId($query->getCompanyId());

        return new GetCompanyProductsQueryResult(
            array_map(
                fn($product) => $this->productTransformer->transform($product),
                $products
            ),
            $total,
            $query->getPage()
        );
    }
}
