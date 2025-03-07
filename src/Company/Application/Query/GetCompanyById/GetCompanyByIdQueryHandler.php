<?php

declare(strict_types=1);

namespace App\Company\Application\Query\GetCompanyById;

use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Application\Transformer\CompanyTransformer;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetCompanyByIdQuery::class)]
class GetCompanyByIdQueryHandler implements QueryHandler
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private CompanyTransformer $transformer
    ) {

    }
    public function __invoke(GetCompanyByIdQuery|Query $query): QueryResult
    {
        $company = $this->companyRepository->findById($query->getCompanyId());

        if ($company === null) {
            throw new EntityNotFoundException($query->getCompanyId());
        }

        return new GetCompanyByIdQueryResult(
            $this->transformer->transform($company)
        );
    }
}
