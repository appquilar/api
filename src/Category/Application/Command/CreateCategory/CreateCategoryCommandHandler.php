<?php

declare(strict_types=1);

namespace App\Category\Application\Command\CreateCategory;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Domain\Entity\Category;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Service\SlugifyServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CreateCategoryCommand::class)]
class CreateCategoryCommandHandler implements CommandHandler
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private SlugifyServiceInterface $slugifyService
    ) {
    }

    public function __invoke(CreateCategoryCommand|Command $command): void
    {
        $slug = $this->slugifyService->generate($command->getName());
        $this->slugifyService->validateSlugIsUnique($slug, $this->categoryRepository);

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
