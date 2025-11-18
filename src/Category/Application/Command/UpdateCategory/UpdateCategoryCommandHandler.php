<?php

declare(strict_types=1);

namespace App\Category\Application\Command\UpdateCategory;

use App\Category\Application\Guard\CategoryParentGuardInterface;
use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Application\Service\GenerateSlugForCategoryService;
use App\Category\Domain\Exception\CategoryCantBeItsOwnParentException;
use App\Category\Domain\Exception\CategoryParentCircularException;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use Doctrine\DBAL\Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateCategoryCommand::class)]
class UpdateCategoryCommandHandler implements CommandHandler
{
    public function __construct(
        private CategoryRepositoryInterface    $categoryRepository,
        private GenerateSlugForCategoryService $generateSlugForCategoryService,
        private CategoryParentGuardInterface   $categoryParentGuard
    ) {
    }

    /**
     * @param UpdateCategoryCommand|Command $command
     * @return void
     * @throws EntityNotFoundException
     * @throws CategoryCantBeItsOwnParentException
     * @throws CategoryParentCircularException
     * @throws Exception
     */
    public function __invoke(UpdateCategoryCommand|Command $command): void
    {
        $category = $this->categoryRepository->findById($command->getCategoryId());
        if ($category === null) {
            throw new EntityNotFoundException($command->getCategoryId());
        }

        $this->categoryParentGuard->assertCanAssignParent($category->getId(), $command->getParentId());

        $category->update(
            $command->getName(),
            $command->getDescription(),
            $this->generateSlugForCategoryService->getCategorySlug(
                $command->getName(),
                $command->getCategoryId(),
            ),
            $command->getParentId(),
            $command->getIcon(),
            $command->getFeaturedImage(),
            $command->getLandscapeImage()
        );

        $this->categoryRepository->save($category);
    }
}
