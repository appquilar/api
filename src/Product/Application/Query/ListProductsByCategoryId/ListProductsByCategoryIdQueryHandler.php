<?php declare(strict_types=1);

namespace App\Product\Application\Query\ListProductsByCategoryId;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ProductCategoryServiceInterface;
use App\Product\Application\Transformer\ProductTransformer;
use App\Product\Domain\Entity\Product;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler(handles: ListProductsByCategoryIdQuery::class)]
class ListProductsByCategoryIdQueryHandler implements QueryHandler
{
    public function __construct(
        private ProductCategoryServiceInterface $productCategoryService,
        private ProductRepositoryInterface $productRepository,
        private ProductTransformer $productTransformer,
    ) {
    }

    /**
     * @param Query|ListProductsByCategoryIdQuery $query
     * @return QueryResult|ListProductsByCategoryIdQueryResult
     */
    public function __invoke(Query|ListProductsByCategoryIdQuery $query): QueryResult|ListProductsByCategoryIdQueryResult
    {
        $categoriesIds = $this->getCategoriesIds($query->getCategoryId());

        $products = $this->productRepository->paginateByCategoryId($categoriesIds, $query->getPage(), $query->getPerPage());
        $total = $this->productRepository->countByCategoryId($categoriesIds);

        return new ListProductsByCategoryIdQueryResult(
            array_map(
                fn(Product $product) => $this->productTransformer->transform($product), $products
            ),
            $total,
            $query->getPage()
        );
    }

    /**
     * @param Uuid $categoryId
     * @return Uuid[]
     */
    public function getCategoriesIds(Uuid $categoryId): array
    {
        return $this->productCategoryService->getCategoriesTrailIds($categoryId);
    }
}
