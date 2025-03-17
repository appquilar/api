<?php

declare(strict_types=1);

namespace App\Company\Application\Query\GetCompanyBySlug;

use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Application\Transformer\CompanyTransformer;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetCompanyBySlugQuery::class)]
class GetCompanyBySlugQueryHandler implements QueryHandler
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private CompanyTransformer $transformer
    ) {

    }
    public function __invoke(GetCompanyBySlugQuery|Query $query): QueryResult
    {
        $company = $this->companyRepository->findOneBy(['slug' => $query->getSlug()]);

        if ($company === null) {
            throw new NotFoundException();
        }

        return new GetCompanyBySlugQueryResult(
            $this->transformer->transform($company)
        );
    }
}
