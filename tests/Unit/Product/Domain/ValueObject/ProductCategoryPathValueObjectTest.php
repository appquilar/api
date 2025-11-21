<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Domain\ValueObject;

use App\Product\Domain\Dto\ProductCategoryPathItemDto;
use App\Product\Domain\ValueObject\ProductCategoryPathValueObject;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProductCategoryPathValueObjectTest extends UnitTestCase
{
    public function test_get_items_returns_same_items_array(): void
    {
        /** @var ProductCategoryPathItemDto|MockObject $item1 */
        $item1 = $this->createMock(ProductCategoryPathItemDto::class);
        /** @var ProductCategoryPathItemDto|MockObject $item2 */
        $item2 = $this->createMock(ProductCategoryPathItemDto::class);

        $items = [$item1, $item2];

        $vo = new ProductCategoryPathValueObject($items);

        $this->assertSame($items, $vo->getItems());
    }

    public function test_to_array_maps_items_to_array(): void
    {
        /** @var ProductCategoryPathItemDto|MockObject $item1 */
        $item1 = $this->createMock(ProductCategoryPathItemDto::class);
        /** @var ProductCategoryPathItemDto|MockObject $item2 */
        $item2 = $this->createMock(ProductCategoryPathItemDto::class);

        $item1Array = ['id' => 'cat-1', 'name' => 'Parent'];
        $item2Array = ['id' => 'cat-2', 'name' => 'Child'];

        $item1->expects($this->once())
            ->method('toArray')
            ->willReturn($item1Array);

        $item2->expects($this->once())
            ->method('toArray')
            ->willReturn($item2Array);

        $vo = new ProductCategoryPathValueObject([$item1, $item2]);

        $this->assertSame(
            [$item1Array, $item2Array],
            $vo->toArray()
        );
    }
}
