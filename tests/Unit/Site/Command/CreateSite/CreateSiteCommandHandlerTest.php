<?php

declare(strict_types=1);

namespace App\Tests\Unit\Site\Command\CreateSite;

use App\Site\Application\Command\CreateSite\CreateSiteCommand;
use App\Site\Application\Command\CreateSite\CreateSiteCommandHandler;
use App\Site\Application\Repository\SiteRepositoryInterface;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class CreateSiteCommandHandlerTest extends UnitTestCase
{
    private SiteRepositoryInterface|MockObject $siteRepositoryMock;
    private CreateSiteCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siteRepositoryMock = $this->createMock(SiteRepositoryInterface::class);
        $this->handler = new CreateSiteCommandHandler($this->siteRepositoryMock);
    }

    public function testCreateSiteSuccessful(): void
    {
        $this->siteRepositoryMock->expects($this->once())
            ->method('save');

        $command = new CreateSiteCommand(Uuid::v4(), 'name', 'title', 'url', 'description', Uuid::v4(), Uuid::v4(), '128aaa', [Uuid::v4()], [Uuid::v4()], [Uuid::v4()]);
        $this->handler->__invoke($command);
    }


}
