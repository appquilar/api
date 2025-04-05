<?php

declare(strict_types=1);

namespace App\Site\Application\Query\GetSiteById;

use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use App\Site\Application\Repository\SiteRepositoryInterface;
use App\Site\Application\Transformer\SiteTransformer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetSiteByIdQuery::class)]
class GetSiteByIdQueryHandler implements QueryHandler
{
    public function __construct(
        private SiteRepositoryInterface $siteRepository,
        private SiteTransformer $transformer,
    ) {
    }

    public function __invoke(GetSiteByIdQuery|Query $query): GetSiteByIdQueryResult|QueryResult
    {
        $site = $this->siteRepository->findById($query->getSiteId());

        if ($site === null) {
            throw new EntityNotFoundException($query->getSiteId());
        }

        return new GetSiteByIdQueryResult($this->transformer->transform($site));
    }
}
