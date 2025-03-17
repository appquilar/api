<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategoryById;

use App\Category\Application\Query\GetCategoryBySlug\GetCategoryBySlugQuery;
use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Application\Transformer\CategoryTransformer;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetCategoryByIdQuery::class)]
class GetCategoryByIdQueryHandler implements QueryHandler
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private CategoryTransformer $categoryTransformer,
    ) {
    }

    public function __invoke(GetCategoryByIdQuery|Query $query): GetCategoryByIdQueryResult|QueryResult
    {
        $category = $this->categoryRepository->findById($query->getCategoryId());

        if ($category === null) {
            throw new EntityNotFoundException($query->getCategoryId());
        }

        return new GetCategoryByIdQueryResult(
            $this->categoryTransformer->transform($category)
        );
    }
}
