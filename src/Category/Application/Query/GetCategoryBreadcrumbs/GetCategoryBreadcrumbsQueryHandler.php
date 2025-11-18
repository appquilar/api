<?php declare(strict_types=1);

namespace App\Category\Application\Query\GetCategoryBreadcrumbs;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Domain\Exception\CategoryParentCircularException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Doctrine\DBAL\Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetCategoryBreadcrumbsQuery::class)]
class GetCategoryBreadcrumbsQueryHandler implements QueryHandler
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * @throws CategoryParentCircularException
     * @throws Exception
     */
    public function __invoke(Query|GetCategoryBreadcrumbsQuery $query): QueryResult|GetCategoryBreadcrumbsQueryResult
    {
        $breadcrumbs = $this->categoryRepository->getParentsFromCategory($query->getCategoryId());

        return new GetCategoryBreadcrumbsQueryResult(
            $breadcrumbs->getBreadcrumbs()
        );
    }
}
