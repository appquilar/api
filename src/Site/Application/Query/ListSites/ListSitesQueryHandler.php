<?php

declare(strict_types=1);

namespace App\Site\Application\Query\ListSites;

use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use App\Site\Application\Repository\SiteRepositoryInterface;
use App\Site\Application\Transformer\SiteTransformer;
use App\Site\Domain\Entity\Site;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: ListSitesQuery::class)]
class ListSitesQueryHandler implements QueryHandler
{
    public function __construct(
        private SiteRepositoryInterface $siteRepository,
        private SiteTransformer $transformer,
    ) {
    }

    public function __invoke(ListSitesQuery|Query $query): ListSitesQueryResult|QueryResult
    {
        return new ListSitesQueryResult(
            array_map(
                function (Site $site) {
                    return $this->transformer->transform($site);
                },
                $this->siteRepository->findAll()
            )
        );
    }
}
