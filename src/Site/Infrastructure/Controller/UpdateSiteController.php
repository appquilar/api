<?php

declare(strict_types=1);

namespace App\Site\Infrastructure\Controller;

use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use App\Site\Application\Command\UpdateSite\UpdateSiteCommand;
use App\Site\Infrastructure\Request\UpdateSiteDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/sites/{site_id}', name: RoutePermission::SITE_UPDATE->value, methods: ['PATCH'])]
class UpdateSiteController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(UpdateSiteDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new UpdateSiteCommand(
                $request->siteId,
                $request->name,
                $request->title,
                $request->url,
                $request->description,
                $request->logoId,
                $request->faviconId,
                $request->primaryColor,
                $request->categoryIds,
                $request->menuCategoryIds,
                $request->featuredCategoryIds,
            )
        );

        return $this->jsonResponseService->noContent();
    }
}
