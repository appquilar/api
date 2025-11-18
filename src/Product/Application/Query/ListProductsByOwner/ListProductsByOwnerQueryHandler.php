<?php declare(strict_types=1);

namespace App\Product\Application\Query\ListProductsByOwner;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Transformer\ProductTransformer;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Enum\ProductOwner;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: ListProductsByOwnerQuery::class)]
class ListProductsByOwnerQueryHandler implements QueryHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductTransformer $productTransformer,
    ) {
    }

    public function __invoke(Query|ListProductsByOwnerQuery $query): QueryResult|ListProductsByOwnerQueryResult
    {
        [$products, $total] = $this->getProductData($query);

        return new ListProductsByOwnerQueryResult(
            array_map(
                fn(Product $product) => $this->productTransformer->transform($product), $products
            ),
            $total,
            $query->getPage()
        );
    }

    private function getProductData(ListProductsByOwnerQuery $query): array
    {
        if ($query->getOwnerType() === ProductOwner::COMPANY) {
            $products = $this->productRepository->paginateByCompanyId(
                $query->getOwnerId(),
                $query->getPage(),
                $query->getPerPage()
            );
            $total = $this->productRepository->countByCompanyId($query->getOwnerId());

            return array($products, $total);
        }

        $products = $this->productRepository->paginateByUserId(
            $query->getOwnerId(),
            $query->getPage(),
            $query->getPerPage()
        );
        $total = $this->productRepository->countByUserId($query->getOwnerId());

        return [$products, $total];
    }
}
