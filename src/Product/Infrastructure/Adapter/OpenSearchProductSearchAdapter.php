<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Adapter;

use App\Product\Application\Adapter\ProductSearchAdapterInterface;
use App\Product\Application\Dto\ProductSearchHitDto;
use App\Product\Domain\ValueObject\PublicationStatus;
use OpenSearch\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final class OpenSearchProductSearchAdapter implements ProductSearchAdapterInterface
{
    public function __construct(
        private Client $client,
        private string $indexName,
    ) {
    }

    /**
     * @param Uuid[]|null $categoryIds
     */
    public function search(
        PublicationStatus $publicationStatus,
        ?string $text = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?int $radiusKm = null,
        ?array $categoryIds = [],
        int $page = 1,
        int $perPage = 10,
    ): array {

        $query = $this->initialiseQuery();
        $query = $this->filterByText($text, $query);
        $query = $this->filterByPublicationStatus($publicationStatus, $query);
        $query = $this->filterByGeoLocation($latitude, $longitude, $radiusKm, $query);
        $query = $this->filterByCategoryIds($categoryIds, $query);

        $response = $this->executeQuery($page, $perPage, $query);

        return [
            'total' => $response['hits']['total']['value'] ?? 0,
            'items' => array_map(
                static fn (array $hit) => ProductSearchHitDto::fromArray($hit['_source']),
                $response['hits']['hits'] ?? []
            ),
        ];
    }

    private function filterByText(?string $text, array $query): array
    {
        if ($text !== null && $text !== '') {
            $query['bool']['must'][] = [
                'multi_match' => [
                    'query' => $text,
                    'fields' => [
                        'name^5',
                        'description^2',
                        'categories.name^3',
                        'categories.description'
                    ],
                    'fuzziness' => 'AUTO',
                ],
            ];
        } else {
            $query['bool']['must'][] = ['match_all' => (object)[]];
        }
        return $query;
    }

    private function filterByPublicationStatus(PublicationStatus $publicationStatus, array $query): array
    {
        $query['bool']['filter'][] = [
            'term' => [
                'publication_status' => $publicationStatus->getStatus(),
            ],
        ];
        return $query;
    }

    private function filterByGeoLocation(?float $latitude, ?float $longitude, ?int $radiusKm, array $query): array
    {
        if ($latitude !== null && $longitude !== null && $radiusKm !== null) {
            $query['bool']['filter'][] = [
                'geo_distance' => [
                    'distance' => sprintf('%skm', $radiusKm),
                    'location' => [
                        'lat' => $latitude,
                        'lon' => $longitude,
                    ],
                ],
            ];
        }

        return $query;
    }

    private function filterByCategoryIds(?array $categoryIds, array $query): array
    {
        if (!empty($categoryIds)) {
            $query['bool']['filter'][] = [
                'nested' => [
                    'path' => 'categories',
                    'query' => [
                        'terms' => [
                            'categories.id' => array_map(
                                fn(Uuid $id) => $id->toString(),
                                $categoryIds
                            ),
                        ],
                    ],
                ],
            ];
        }

        return $query;
    }

    private function executeQuery(int $page, int $perPage, array $query): string|null|iterable
    {
        $params = [
            'index' => $this->indexName,
            'body' => [
                'from' => max(0, ($page - 1) * $perPage),
                'size' => $perPage,
                'track_total_hits' => true,
                'query' => $query,
            ],
        ];

        return $this->client->search($params);
    }

    private function initialiseQuery(): array
    {
        return [
            'bool' => [
                'must' => [],
                'filter' => []
            ]
        ];
    }
}
