<?php declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

use App\Product\Domain\Dto\ProductCategoryPathItemDto;
use Symfony\Component\Uid\Uuid;

final readonly class ProductCategoryPathValueObject
{
    /**
     * @param ProductCategoryPathItemDto[] $items
     */
    public function __construct(
        private array $items
    ) {
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return array_map(
            fn (ProductCategoryPathItemDto $item) => $item->toArray(),
            $this->items
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            array_map(
                function (array $item): ProductCategoryPathItemDto {
                    return new ProductCategoryPathItemDto(
                        Uuid::fromString($item['id']),
                        $item['slug'],
                        $item['name'],
                        $item['description'],
                        $item['parent_id'] !== null ? Uuid::fromString($item['parent_id']) : null
                    );
                },
                $data
            )
        );
    }
}
