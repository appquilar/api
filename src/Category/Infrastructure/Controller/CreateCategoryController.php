<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Controller;

use App\Category\Application\Command\CreateCategory\CreateCategoryCommand;
use App\Category\Infrastructure\Request\CreateCategoryDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories', name: RoutePermission::CATEGORY_CREATE->value, methods: ['POST'])]
class CreateCategoryController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(CreateCategoryDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new CreateCategoryCommand(
                $request->categoryId,
                $request->name,
                $request->description,
                $request->parentId,
                $request->iconId,
                $request->featuredImageId,
                $request->landscapeImageId
            )
        );

        return $this->responseService->created();
    }
}
