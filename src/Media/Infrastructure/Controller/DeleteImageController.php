<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Controller;

use App\Media\Application\Command\DeleteImage\DeleteImageCommand;
use App\Media\Infrastructure\Request\DeleteImageDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/media/images/{image_id}', name: RoutePermission::MEDIA_DELETE_IMAGE->value, methods: ['DELETE'])]
class DeleteImageController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $jsonResponseService
    ) {}

    public function __invoke(DeleteImageDto $request): JsonResponse
    {
        $this->commandBus->dispatch(new DeleteImageCommand($request->imageId));

        return $this->jsonResponseService->noContent();
    }
}
