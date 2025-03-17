<?php

declare(strict_types=1);

namespace App\Tests\Unit\Category\Query\GetCategoryById;

use App\Category\Application\Query\GetCategoryById\GetCategoryByIdQuery;
use App\Category\Application\Query\GetCategoryById\GetCategoryByIdQueryHandler;
use App\Category\Application\Query\GetCategoryById\GetCategoryByIdQueryResult;
use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Application\Transformer\CategoryTransformer;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Tests\Factories\Category\Domain\Entity\CategoryFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class GetCategoryByIdQueryHandlerTest extends UnitTestCase
{
    private CategoryRepositoryInterface|MockObject $categoryRepositoryMock;
    private CategoryTransformer|MockObject $categoryTransformerMock;
    private GetCategoryByIdQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepositoryMock = $this->createMock(CategoryRepositoryInterface::class);
        $this->categoryTransformerMock = $this->createMock(CategoryTransformer::class);
        $this->handler = new GetCategoryByIdQueryHandler(
            $this->categoryRepositoryMock,
            $this->categoryTransformerMock
        );
    }

    public function testInvoke(): void
    {
        $categoryId = Uuid::v4();
        $category = CategoryFactory::createOne(['categoryId' => $categoryId]);
        $query = new GetCategoryByIdQuery($categoryId);
        $response = ['category_id' => $category->getId()->toString()];

        $this->categoryRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($query->getCategoryId())
            ->willReturn($category);
        $this->categoryTransformerMock->expects($this->once())
            ->method('transform')
            ->with($category)
            ->willReturn($response);

        $result = $this->handler->__invoke($query);

        $this->assertInstanceOf(GetCategoryByIdQueryResult::class, $result);
        $this->assertArrayHasKey('category_id', $result->getCategory());
    }

    public function testCategoryNotFound(): void
    {
        $categoryId = Uuid::v4();

        $query = new GetCategoryByIdQuery($categoryId);

        $this->categoryRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($query->getCategoryId())
            ->willReturn(null);
        $this->categoryTransformerMock->expects($this->never())
            ->method('transform');

        $this->expectException(EntityNotFoundException::class);
        $this->handler->__invoke($query);
    }
}
