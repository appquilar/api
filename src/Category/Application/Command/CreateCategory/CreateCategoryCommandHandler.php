<?php

declare(strict_types=1);

namespace App\Category\Application\Command\CreateCategory;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Application\Service\GenerateSlugForCategoryService;
use App\Category\Domain\Entity\Category;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CreateCategoryCommand::class)]
class CreateCategoryCommandHandler implements CommandHandler
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private GenerateSlugForCategoryService $generateSlugForCategoryService,
    ) {
    }

    public function __invoke(CreateCategoryCommand|Command $command): void
    {
        $slug = $this->generateSlugForCategoryService->getCategorySlug(
            $command->getName(),
        );

        $category = new Category(
            $command->getCategoryId(),
            $command->getName(),
            $command->getDescription(),
            $slug,
            $command->getParentId(),
            $command->getIcon(),
            $command->getFeaturedImage(),
            $command->getLandscapeImage()
        );

        $this->categoryRepository->save($category);
    }
}
