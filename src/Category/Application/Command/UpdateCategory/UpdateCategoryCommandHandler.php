<?php

declare(strict_types=1);

namespace App\Category\Application\Command\UpdateCategory;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Service\SlugifyServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateCategoryCommand::class)]
class UpdateCategoryCommandHandler implements CommandHandler
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private SlugifyServiceInterface $slugifyService
    ) {
    }

    public function __invoke(UpdateCategoryCommand|Command $command): void
    {
        $slug = $this->slugifyService->generate($command->getSlug());
        $this->slugifyService->validateSlugIsUnique($slug, $this->categoryRepository, $command->getCategoryId());

        $category = $this->categoryRepository->findById($command->getCategoryId());
        if ($category === null) {
            throw new EntityNotFoundException($command->getCategoryId());
        }

        $category->update(
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
