<?php

declare(strict_types=1);

namespace App\Media\Application\Query\GetImage;

use App\Media\Application\Repository\ImageRepositoryInterface;
use App\Media\Application\Service\StorageServiceInterface;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetImageQuery::class)]
class GetImageQueryHandler implements QueryHandler
{
    public function __construct(
        private ImageRepositoryInterface $imageRepository,
        private StorageServiceInterface $storageService,
    ) {
    }

    public function __invoke(GetImageQuery|Query $query): GetImageQueryResult
    {
        $image = $this->imageRepository->findById($query->getImageId());

        if ($image === null) {
            throw new NotFoundException();
        }

        return new GetImageQueryResult(
            $this->storageService->getImagePath($image->getId(), $query->getSize()),
        );
    }
}
