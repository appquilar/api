<?php declare(strict_types=1);

namespace App\Product\Infrastructure\OpenSearch;

use App\Product\Application\Service\ProductSearchIndexerInterface;
use OpenSearch\Client;

class OpenSearchProductIndexer implements ProductSearchIndexerInterface
{
    public function __construct(
        private Client $client,
        private string $indexName = 'products',
    ) {
        $this->ensureIndex();
    }

    public function index(string $id, array $document): void
    {
        $this->client->index([
            'index' => $this->indexName,
            'id'    => $id,
            'body'  => $document,
        ]);
    }

    private function ensureIndex(): void
    {
        $exists = $this->client->indices()->exists(['index' => $this->indexName]);
        if (!$exists) {
            $this->client->indices()->create([
                'index' => $this->indexName,
                'body'  => [
                    'settings' => [
                        'number_of_shards'   => 1,
                        'number_of_replicas' => 0,
                        'analysis' => [
                            'analyzer' => [
                                'default' => [
                                    'type'      => 'standard',
                                    'stopwords' => '_spanish_',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'name' => [
                                'type'     => 'text',
                                'analyzer' => 'spanish',
                            ],
                            'description' => [
                                'type'     => 'text',
                                'analyzer' => 'spanish',
                            ],
                            'location' => [
                                'type' => 'geo_point',
                            ],
                            'categories' => [
                                'type'       => 'nested',
                                'properties' => [
                                    'id'          => ['type' => 'keyword'],
                                    'slug'        => ['type' => 'text', 'analyzer' => 'spanish'],
                                    'name'        => ['type' => 'text', 'analyzer' => 'spanish'],
                                    'description' => ['type' => 'text', 'analyzer' => 'spanish'],
                                ],
                            ],
                            'publication_status' => [
                                'type' => 'text',
                            ],
                            'owner_id' => [
                                'type' => 'keyword'
                            ],
                            'owner_type' => [
                                'type' => 'text',
                                'analyzer' => 'spanish'
                            ],
                            'slug' => ['type' => 'text', 'analyzer' => 'spanish'],
                            'imageIds' => ['type' => 'keyword']
                        ],
                    ],
                ],
            ]);
        }
    }
}
