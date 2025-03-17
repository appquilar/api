<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Controller;

use App\Category\Application\Command\UpdateCategory\UpdateCategoryCommand;
use App\Category\Infrastructure\Request\UpdateCategoryDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories/{category_id}', name: RoutePermission::CATEGORY_UPDATE->value, methods: ['PATCH'])]
class UpdateCategoryController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(UpdateCategoryDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new UpdateCategoryCommand(
                $request->categoryId,
                $request->name,
                $request->slug,
                $request->description,
                $request->parentId,
                $request->iconId,
                $request->featuredImageId,
                $request->landscapeImageId
            )
        );

        return $this->responseService->noContent();
    }

}
