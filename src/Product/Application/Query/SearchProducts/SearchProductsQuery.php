<?php declare(strict_types=1);

namespace App\Product\Application\Query\SearchProducts;

use App\Product\Domain\ValueObject\PublicationStatus;
use App\Shared\Application\Query\PaginatedQuery;

class SearchProductsQuery extends PaginatedQuery
{
    public function __construct(
        private ?string $text,
        private ?float  $latitude,
        private ?float  $longitude,
        private ?int  $radiusKm,
        private PublicationStatus $publicationStatus,
        private array   $categoryIds = [],
        int             $page = 1,
        int             $perPage = 10,
    ) {
        parent::__construct($page, $perPage);
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getRadiusKm(): ?int
    {
        return $this->radiusKm;
    }

    public function getCategoryIds(): array
    {
        return $this->categoryIds;
    }

    public function getPublicationStatus(): PublicationStatus
    {
        return $this->publicationStatus;
    }
}
