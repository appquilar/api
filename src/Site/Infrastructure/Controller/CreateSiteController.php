<?php

declare(strict_types=1);

namespace App\Site\Infrastructure\Controller;

use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use App\Site\Application\Command\CreateSite\CreateSiteCommand;
use App\Site\Infrastructure\Request\CreateSiteDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/sites', name: RoutePermission::SITE_CREATE->value, methods: ['POST'])]
class CreateSiteController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(CreateSiteDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new CreateSiteCommand(
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

        return $this->jsonResponseService->created();
    }
}
