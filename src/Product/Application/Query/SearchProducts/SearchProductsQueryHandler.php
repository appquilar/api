<?php declare(strict_types=1);

namespace App\Product\Application\Query\SearchProducts;

use App\Product\Application\Adapter\ProductSearchAdapterInterface;
use App\Product\Application\Dto\ProductSearchHitDto;
use App\Product\Application\Transformer\ProductHitTransformer;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use App\Shared\Domain\ValueObject\GeoLocation;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: SearchProductsQuery::class)]
class SearchProductsQueryHandler implements QueryHandler
{
    public function __construct(
        private ProductSearchAdapterInterface $productSearchAdapter,
        private ProductHitTransformer $productHitTransformer,
    ) {
    }

    public function __invoke(Query|SearchProductsQuery $query): QueryResult|SearchProductsQueryResult
    {
        $results = $this->productSearchAdapter->search(
            $query->getPublicationStatus(),
            $query->getText(),
            $query->getLatitude(),
            $query->getLongitude(),
            $query->getRadiusKm(),
            $query->getCategoryIds(),
            $query->getPage(),
            $query->getPerPage()
        );

        $origin = null;
        if ($query->getLatitude() !== null && $query->getLongitude() !== null) {
            $origin = new GeoLocation($query->getLatitude(), $query->getLongitude());
        }

        return new SearchProductsQueryResult(
            array_map(
                fn(ProductSearchHitDto $product): array => $this->productHitTransformer->transform($product, $origin),
                $results['items']
            ),
            $results['total'],
            $query->getPage()
        );
    }
}
