<?php

declare(strict_types=1);

namespace App\Tests\Unit\Category\Application\Command\UpdateCategory;

use App\Category\Application\Command\UpdateCategory\UpdateCategoryCommand;
use App\Category\Application\Command\UpdateCategory\UpdateCategoryCommandHandler;
use App\Category\Application\Guard\CategoryParentGuardInterface;
use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Application\Service\GenerateSlugForCategoryService;
use App\Category\Domain\Entity\Category;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Tests\Factories\Category\Domain\Entity\CategoryFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class UpdateCategoryCommandHandlerTest extends UnitTestCase
{
    private CategoryRepositoryInterface|MockObject $categoryRepositoryMock;
    private GenerateSlugForCategoryService|MockObject $generateSlugForCategoryService;
    private CategoryParentGuardInterface|MockObject $categoryParentGuard;
    private UpdateCategoryCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepositoryMock = $this->createMock(CategoryRepositoryInterface::class);
        $this->generateSlugForCategoryService = $this->createMock(GenerateSlugForCategoryService::class);
        $this->categoryParentGuard = $this->createMock(CategoryParentGuardInterface::class);

        $this->handler = new UpdateCategoryCommandHandler(
            $this->categoryRepositoryMock,
            $this->generateSlugForCategoryService,
            $this->categoryParentGuard
        );
    }

    public function testUpdateCategory(): void
    {
        $categoryId = Uuid::v4();
        $category = CategoryFactory::createOne(['categoryId' => $categoryId]);
        $name = 'new name';
        $slug = 'new-name';
        $parentId = Uuid::v4();
        $command = new UpdateCategoryCommand(
            $categoryId,
            $name,
            'new-description',
            $parentId,
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4()
        );

        $this->givenItExistsACategory($categoryId, $category);

        $this->generateSlugForCategoryService
            ->expects($this->once())
            ->method('getCategorySlug')
            ->with($name, $categoryId)
            ->willReturn($slug);

        $this->givenIValidateCircularIssuesWhenUpdatingParentId($categoryId, $parentId);

        $this->categoryRepositoryMock->expects($this->once())
            ->method('save')
            ->with($category);

        $this->handler->__invoke($command);
    }

    public function testCategoryNotFound(): void
    {
        $categoryId = Uuid::v4();
        $command = new UpdateCategoryCommand(
            $categoryId,
            'new name',
            'new-description',
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4()
        );

        $this->givenItExistsACategory($categoryId, null);

        $this->expectException(EntityNotFoundException::class);
        $this->handler->__invoke($command);
    }

    private function givenItExistsACategory(Uuid $categoryId, ?Category $category): void
    {
        $this->categoryRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($categoryId)
            ->willReturn($category);
    }

    private function givenIValidateCircularIssuesWhenUpdatingParentId(
        Uuid $categoryId, ?Uuid $categoryParentId
    ): void
    {
        $this->categoryParentGuard->expects($this->once())
            ->method('assertCanAssignParent')
            ->with($categoryId, $categoryParentId);
    }
}
