<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Controller;

use App\Media\Application\Query\GetImage\GetImageQuery;
use App\Media\Application\Query\GetImage\GetImageQueryResult;
use App\Media\Infrastructure\Request\GetImageDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/media/images/{image_id}/{size}', methods: ['GET'])]
class GetImageController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(GetImageDto $request): BinaryFileResponse
    {
        /** @var GetImageQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new GetImageQuery($request->imageId, $request->imageSize)
        );

        return $this->responseService->respondImage($queryResult->getPath());
    }
}
