<?php declare(strict_types=1);

namespace App\Product\Infrastructure\OpenSearch;

use OpenSearch\Client;
use OpenSearch\ClientBuilder;
use OpenSearch\GuzzleClientFactory;

class OpenSearchClientFactory
{
    public static function create(string $host): Client
    {
        $factory = new GuzzleClientFactory();

        return $factory->create([
            'base_uri' => $host,
        ]);
    }
}
