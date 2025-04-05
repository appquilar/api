<?php

declare(strict_types=1);

namespace App\Site\Application\Command\UpdateSite;

use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Site\Application\Repository\SiteRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateSiteCommand::class)]
class UpdateSiteCommandHandler implements CommandHandler
{
    public function __construct(
        private SiteRepositoryInterface $siteRepository,
    ) {
    }

    public function __invoke(UpdateSiteCommand|Command $command): void
    {
        $site = $this->siteRepository->findById($command->getSiteId());
        if ($site === null) {
            throw new EntityNotFoundException($command->getSiteId());
        }

        $site->update(
            $command->getName(),
            $command->getTitle(),
            $command->getUrl(),
            $command->getDescription(),
            $command->getLogoId(),
            $command->getFaviconId(),
            $command->getPrimaryColor(),
            $command->getCategoryIds(),
            $command->getMenuCategoryIds(),
            $command->getFeaturedCategoryIds(),
        );

        $this->siteRepository->save($site);
    }
}
