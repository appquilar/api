<?php declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Tests\Integration\IntegrationTestCase;
use OpenSearch\Client;

class ProductIntegrationTestCase extends IntegrationTestCase
{
    private Client $openSearchClient;
    private string $indexName;

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();
        $this->openSearchClient = $container->get(Client::class);
        $this->indexName = $container->getParameter('opensearch_products_index');
        $this->cleanOpenSearchIndex();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanOpenSearchIndex();
    }


    private function cleanOpenSearchIndex(): void
    {
        if ($this->openSearchClient->indices()->exists(['index' => $this->indexName])) {
            $this->openSearchClient->indices()->delete(['index' => $this->indexName]);
        }
    }

    protected function refreshOpenSearchIndex(): void
    {
        $this->openSearchClient->indices()->refresh(['index' => $this->indexName]);
    }
}
