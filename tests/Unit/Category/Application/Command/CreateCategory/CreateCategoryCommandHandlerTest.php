<?php

declare(strict_types=1);

namespace App\Tests\Unit\Category\Application\Command\CreateCategory;

use App\Category\Application\Command\CreateCategory\CreateCategoryCommand;
use App\Category\Application\Command\CreateCategory\CreateCategoryCommandHandler;
use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Service\SlugifyServiceInterface;
use App\Tests\Factories\Category\Domain\Entity\CategoryFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class CreateCategoryCommandHandlerTest extends UnitTestCase
{
    private CategoryRepositoryInterface|MockObject $categoryRepositoryMock;
    private SlugifyServiceInterface|MockObject $slugifyServiceMock;
    private CreateCategoryCommandHandler $handler;

    protected function setUp(): void
    {
        $this->categoryRepositoryMock = $this->createMock(CategoryRepositoryInterface::class);
        $this->slugifyServiceMock = $this->createMock(SlugifyServiceInterface::class);

        $this->handler = new CreateCategoryCommandHandler(
            $this->categoryRepositoryMock,
            $this->slugifyServiceMock
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

        $this->slugifyServiceMock
            ->expects($this->once())
            ->method('generate')
            ->with($name)
            ->willReturn($slug);

        $this->slugifyServiceMock
            ->expects($this->once())
            ->method('validateSlugIsUnique')
            ->with($slug, $this->categoryRepositoryMock);

        $this->categoryRepositoryMock
            ->expects($this->once())
            ->method('save');

        $this->handler->__invoke($command);
    }

    public function testSlugIsNotUnique(): void
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

        $this->slugifyServiceMock
            ->expects($this->once())
            ->method('generate')
            ->with($name)
            ->willReturn($slug);

        $this->slugifyServiceMock
            ->expects($this->once())
            ->method('validateSlugIsUnique')
            ->willThrowException(new BadRequestException());

        $this->expectException(BadRequestException::class);
        $this->handler->__invoke($command);
    }
}
