<?php declare(strict_types=1);

namespace App\Product\Application\Service;

interface ProductSearchIndexerInterface
{
    public function index(string $id, array $document): void;
}
