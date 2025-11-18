<?php declare(strict_types=1);

namespace App\Product\Application\Service;

interface ShortIdGeneratorInterface
{
    public function generateShortId(): string;
}
