<?php declare(strict_types=1);

namespace App\Category\Domain\ValueObject;

use App\Category\Domain\Dto\CategoryPathItemDto;
use App\Category\Domain\Exception\CategoryParentCircularException;
use Symfony\Component\Uid\Uuid;

class CategoryPathValueObject
{
    /**
     * @param CategoryPathItemDto[] $items
     * @throws CategoryParentCircularException
     */
    public function __construct(
        private array $items
    ) {
        $this->assertNoDuplicateIds();
    }

    /**
     * @throws CategoryParentCircularException
     */
    public static function fromItems(array $items): self
    {
        return new self(
            array_map(
                fn (array $item): CategoryPathItemDto => CategoryPathItemDto::fromArray($item),
                $items
            )
        );
    }

    /**
     * @return void
     * @throws CategoryParentCircularException
     */
    private function assertNoDuplicateIds(): void
    {
        $seen = [];

        foreach ($this->items as $item) {
            $key = $item->getId()->toString();
            if (array_key_exists($key, $seen)) {
                throw new CategoryParentCircularException('category.update.parent_id.circular');
            }
            $seen[$key] = true;
        }
    }

    public function getBreadcrumbs(): array
    {
        $sorted = $this->items;
        usort($sorted, static fn (CategoryPathItemDto $a, CategoryPathItemDto $b) =>
            $b->depth <=> $a->depth
        );

        return array_map(
            function (CategoryPathItemDto $item): array {
                return [
                    'id' => $item->getId()->toString(),
                    'parent_id' => $item->getParentId()?->toString(),
                    'name' => $item->getName(),
                    'slug' => $item->getSlug(),
                    'icon_id' => $item->getId(),
                    'depth' => $item->getDepth()
                ];
            }, $sorted
        );
    }

    public function containsCategory(Uuid $id): bool
    {
        return array_any($this->items, fn($item) => $item->id->equals($id));
    }
}
