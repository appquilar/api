<?php

declare(strict_types=1);

namespace App\Tests\Unit\Category\Query\GetCategoryBySlug;

use App\Category\Application\Query\GetCategoryBySlug\GetCategoryBySlugQuery;
use App\Category\Application\Query\GetCategoryBySlug\GetCategoryBySlugQueryHandler;
use App\Category\Application\Query\GetCategoryBySlug\GetCategoryBySlugQueryResult;
use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Application\Transformer\CategoryTransformer;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Tests\Factories\Category\Domain\Entity\CategoryFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class GetCategoryBySlugQueryHandlerTest extends UnitTestCase
{
    private CategoryRepositoryInterface|MockObject $categoryRepositoryMock;
    private CategoryTransformer|MockObject $categoryTransformerMock;
    private GetCategoryBySlugQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepositoryMock = $this->createMock(CategoryRepositoryInterface::class);
        $this->categoryTransformerMock = $this->createMock(CategoryTransformer::class);
        $this->handler = new GetCategoryBySlugQueryHandler(
            $this->categoryRepositoryMock,
            $this->categoryTransformerMock
        );
    }

    public function testInvoke(): void
    {
        $slug = 'new-slug';
        $category = CategoryFactory::createOne(['slug' => $slug]);
        $query = new GetCategoryBySlugQuery($slug);
        $response = ['slug' => $slug];

        $this->categoryRepositoryMock->expects($this->once())
            ->method('findBySlug')
            ->with($query->getSlug())
            ->willReturn($category);
        $this->categoryTransformerMock->expects($this->once())
            ->method('transform')
            ->with($category)
            ->willReturn($response);

        $result = $this->handler->__invoke($query);

        $this->assertInstanceOf(GetCategoryBySlugQueryResult::class, $result);
        $this->assertArrayHasKey('slug', $result->getCategory());
    }

    public function testCategoryNotFound(): void
    {
        $slug = 'new-slug';
        $query = new GetCategoryBySlugQuery($slug);

        $this->categoryRepositoryMock->expects($this->once())
            ->method('findBySlug')
            ->with($query->getSlug())
            ->willReturn(null);
        $this->categoryTransformerMock->expects($this->never())
            ->method('transform');

        $this->expectException(NotFoundException::class);
        $this->handler->__invoke($query);
    }
}
