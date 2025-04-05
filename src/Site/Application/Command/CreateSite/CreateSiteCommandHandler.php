<?php

declare(strict_types=1);

namespace App\Site\Application\Command\CreateSite;

use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Site\Application\Repository\SiteRepositoryInterface;
use App\Site\Domain\Entity\Site;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CreateSiteCommand::class)]
class CreateSiteCommandHandler implements CommandHandler
{
    public function __construct(
        private SiteRepositoryInterface $siteRepository
    ) {
    }

    public function __invoke(CreateSiteCommand|Command $command): void
    {
        $site = new Site(
            $command->getSiteId(),
            $command->getName(),
            $command->getTitle(),
            $command->getUrl(),
            $command->getDescription(),
            $command->getLogoId(),
            $command->getFaviconId(),
            $command->getPrimaryColor(),
            $command->getCategoryIds(),
            $command->getMenuCategoryIds(),
            $command->getFeaturedCategoryIds()
        );

        $this->siteRepository->save($site);
    }
}
