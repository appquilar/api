<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Controller;

use App\Media\Application\Command\UploadImage\UploadImageCommand;
use App\Media\Infrastructure\Request\UploadImageDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/media/images/upload', name: RoutePermission::MEDIA_UPLOAD_IMAGE->value, methods: ['POST'])]
class UploadImageController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $jsonResponseService
    ) {}

    public function __invoke(UploadImageDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new UploadImageCommand(
                $request->imageId,
                $request->file
            )
        );

        return $this->jsonResponseService->created();
    }
}
