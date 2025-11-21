<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Command\PublishProduct;

use App\Product\Application\Command\PublishProduct\PublishProductCommand;
use App\Product\Application\Command\PublishProduct\PublishProductCommandHandler;
use App\Tests\Unit\Product\Application\Command\TestProductCommandHandler;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use Symfony\Component\Uid\Uuid;

class PublishTestProductCommandHandler extends TestProductCommandHandler
{
    private PublishProductCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new PublishProductCommandHandler(
            $this->productRepository,
            $this->productAuthorizationService,
            $this->eventDispatcher
        );
    }

    public function test_it_publishes_product_and_saves_and_dispatches_event(): void
    {
        $productId = Uuid::v4();

        $product = $this->givenAProductExists($productId);
        $this->givenCanEditProduct($product);
        $this->givenISaveTheProduct($product);
        $this->givenDispatchProductUpdateEvent($productId);

        /** @var PublishProductCommand $command */
        $command = $this->createMock(PublishProductCommand::class);
        $command->method('getProductId')->willReturn($productId);

        ($this->handler)($command);
    }

    public function test_it_throws_exception_when_product_not_found(): void
    {
        $productId = Uuid::v4();

        $this->givenAProductNotExists($productId);

        /** @var PublishProductCommand $command */
        $command = $this->createMock(PublishProductCommand::class);
        $command->method('getProductId')->willReturn($productId);

        $this->productRepository
            ->expects($this->never())
            ->method('save');

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $this->expectException(EntityNotFoundException::class);

        ($this->handler)($command);
    }

    public function test_it_throws_exception_when_user_cant_edit_product(): void
    {
        $productId = Uuid::v4();

        $product = $this->givenAProductExists($productId);
        $this->givenCantEditProduct($product);

        /** @var PublishProductCommand $command */
        $command = $this->createMock(PublishProductCommand::class);
        $command->method('getProductId')->willReturn($productId);

        $this->productRepository
            ->expects($this->never())
            ->method('save');

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $this->expectException(UnauthorizedException::class);

        ($this->handler)($command);
    }
}
