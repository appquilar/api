<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Service;

use App\Product\Application\Service\ShortIdGeneratorInterface;
use Hidehalo\Nanoid\Client;

class NanoidShortIdGenerator implements ShortIdGeneratorInterface
{
    private const ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    private const SIZE = 12;

    private Client $client;

    public function __construct() {
        $this->client = new Client();
    }

    public function generateShortId(): string
    {
        return $this->client->formatedId(self::ALPHABET, self::SIZE);
    }
}
