<?php

declare(strict_types=1);

namespace App\Tests\Unit\Category\Application\Command\CreateCategory;

use App\Category\Application\Command\CreateCategory\CreateCategoryCommand;
use App\Category\Application\Command\CreateCategory\CreateCategoryCommandHandler;
use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Application\Service\GenerateSlugForCategoryService;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class CreateCategoryCommandHandlerTest extends UnitTestCase
{
    private CategoryRepositoryInterface|MockObject $categoryRepositoryMock;
    private GenerateSlugForCategoryService|MockObject $generateSlugForCategoryService;
    private CreateCategoryCommandHandler $handler;

    protected function setUp(): void
    {
        $this->categoryRepositoryMock = $this->createMock(CategoryRepositoryInterface::class);
        $this->generateSlugForCategoryService = $this->createMock(GenerateSlugForCategoryService::class);

        $this->handler = new CreateCategoryCommandHandler(
            $this->categoryRepositoryMock,
            $this->generateSlugForCategoryService
        );
    }

    public function testCreateCategorySuccessfully(): void
    {
        $categoryId = Uuid::v4();
        $name = "Test Category";
        $slug = "test-category";

        $command = new CreateCategoryCommand(
            $categoryId,
            $name,
            'description description',
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4()
        );

        $this->generateSlugForCategoryService
            ->expects($this->once())
            ->method('getCategorySlug')
            ->with($name)
            ->willReturn($slug);

        $this->categoryRepositoryMock
            ->expects($this->once())
            ->method('save');

        $this->handler->__invoke($command);
    }
}
