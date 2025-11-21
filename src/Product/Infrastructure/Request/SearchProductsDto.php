<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;

class SearchProductsDto implements RequestDtoInterface
{
    /**
     * @param Uuid[]|null $categories
     */
    public function __construct(
        public ?string $text = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?int $radius = 50,
        public ?array $categories = [],
        public ?int $page = 1,
        public ?int $perPage = 10,
    ) {
    }
}
