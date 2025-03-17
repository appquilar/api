<?php

declare(strict_types=1);

namespace App\Tests\Unit\Category\Application\Command\UpdateCategory;

use App\Category\Application\Command\CreateCategory\CreateCategoryCommand;
use App\Category\Application\Command\UpdateCategory\UpdateCategoryCommand;
use App\Category\Application\Command\UpdateCategory\UpdateCategoryCommandHandler;
use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Service\SlugifyServiceInterface;
use App\Tests\Factories\Category\Domain\Entity\CategoryFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class UpdateCategoryCommandHandlerTest extends UnitTestCase
{
    private CategoryRepositoryInterface|MockObject $categoryRepositoryMock;
    private SlugifyServiceInterface|MockObject $slugifyServiceMock;
    private UpdateCategoryCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepositoryMock = $this->createMock(CategoryRepositoryInterface::class);
        $this->slugifyServiceMock = $this->createMock(SlugifyServiceInterface::class);
        $this->handler = new UpdateCategoryCommandHandler(
            $this->categoryRepositoryMock,
            $this->slugifyServiceMock
        );
    }

    public function testUpdateCategory(): void
    {
        $categoryId = Uuid::v4();
        $category = CategoryFactory::createOne(['categoryId' => $categoryId]);
        $slug = 'new-slug';
        $command = new UpdateCategoryCommand(
            $categoryId,
            'new name',
            $slug,
            'new-description',
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4()
        );

        $this->slugifyServiceMock->expects($this->once())
            ->method('generate')
            ->with($slug)
            ->willReturn($slug);

        $this->slugifyServiceMock->expects($this->once())
            ->method('validateSlugIsUnique')
            ->with($slug);

        $this->categoryRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($categoryId)
            ->willReturn($category);

        $this->categoryRepositoryMock->expects($this->once())
            ->method('save')
            ->with($category);

        $this->handler->__invoke($command);
    }

    public function testSlugIsNotUnique(): void
    {
        $categoryId = Uuid::v4();
        $name = "Test Category";
        $slug = "test-category";

        $command = new UpdateCategoryCommand(
            $categoryId,
            'new name',
            $slug,
            'new-description',
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4()
        );

        $this->slugifyServiceMock
            ->expects($this->once())
            ->method('generate')
            ->with($slug)
            ->willReturn($slug);

        $this->slugifyServiceMock
            ->expects($this->once())
            ->method('validateSlugIsUnique')
            ->willThrowException(new BadRequestException());

        $this->expectException(BadRequestException::class);
        $this->handler->__invoke($command);
    }

    public function testCategoryNotFound(): void
    {
        $categoryId = Uuid::v4();
        $slug = 'new-slug';
        $command = new UpdateCategoryCommand(
            $categoryId,
            'new name',
            $slug,
            'new-description',
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4(),
            Uuid::v4()
        );

        $this->slugifyServiceMock->expects($this->once())
            ->method('generate')
            ->with($slug)
            ->willReturn($slug);

        $this->slugifyServiceMock->expects($this->once())
            ->method('validateSlugIsUnique')
            ->with($slug);

        $this->categoryRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($categoryId)
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->handler->__invoke($command);
    }
}
