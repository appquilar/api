<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Command\UpdateProduct;

use App\Product\Application\Assembler\TierAssembler;
use App\Product\Application\Command\UpdateProduct\UpdateProductCommand;
use App\Product\Application\Command\UpdateProduct\UpdateProductCommandHandler;
use App\Product\Application\Service\SlugForProductsManager;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\ValueObject\TierCollection;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Request\Input\MoneyInput;
use App\Tests\Unit\Product\Application\Command\TestProductCommandHandler;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class UpdateTestProductCommandHandler extends TestProductCommandHandler
{
    /** @var SlugForProductsManager|MockObject */
    private SlugForProductsManager|MockObject $slugForProductsManager;

    /** @var TierAssembler|MockObject */
    private TierAssembler|MockObject $tierAssembler;

    private UpdateProductCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugForProductsManager = $this->createMock(SlugForProductsManager::class);
        $this->tierAssembler = $this->createMock(TierAssembler::class);

        $this->handler = new UpdateProductCommandHandler(
            $this->productRepository,
            $this->productAuthorizationService,
            $this->eventDispatcher,
            $this->slugForProductsManager,
            $this->tierAssembler
        );
    }

    public function test_it_updates_product_and_saves_and_dispatches_event(): void
    {
        $productId = Uuid::v4();

        /** @var Product $product */
        $product = $this->givenAProductExists($productId);
        $this->givenCanEditProduct($product);

        $newName        = 'New Product Name';
        $newInternalId  = 'NEW-INT-ID';
        $newDescription = 'New description for the product';
        $newCategoryId  = Uuid::v4();
        $newImageIds    = [Uuid::v4(), Uuid::v4()];
        $newQuantity    = 10;

        // Usamos los tiers existentes del producto para garantizar formato válido
        $tierInputs      = $product->getTiers()->toArray();
        $tierCollection  = TierCollection::fromArray($tierInputs);
        $generatedSlug   = 'new-product-slug';
        $depositMoney    = new Money(1000, 'EUR');

        $depositInput = new MoneyInput(1000, 'EUR');

        $command = new UpdateProductCommand(
            $productId,
            $newName,
            $newInternalId,
            $newDescription,
            $depositInput,
            $tierInputs,
            $newQuantity,
            $newCategoryId,
            $newImageIds
        );

        // Slug generado en base al nombre y al shortId actual del producto
        $this->slugForProductsManager
            ->expects($this->once())
            ->method('generateSlugForProduct')
            ->with($newName, $product->getShortId())
            ->willReturn($generatedSlug);

        // TierAssembler debe devolver un TierCollection válido
        $this->tierAssembler
            ->expects($this->once())
            ->method('createTierCollectionFromArrayOfTierInputs')
            ->with($tierInputs)
            ->willReturn($tierCollection);

        $this->givenISaveTheProduct($product);
        $this->givenDispatchProductUpdateEvent($productId);

        ($this->handler)($command);
    }

    public function test_it_throws_exception_when_product_not_found(): void
    {
        $productId = Uuid::v4();

        $this->givenAProductNotExists($productId);

        /** @var UpdateProductCommand $command */
        $command = $this->createMock(UpdateProductCommand::class);
        $command->method('getProductId')->willReturn($productId);

        $this->productRepository
            ->expects($this->never())
            ->method('save');

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $this->slugForProductsManager
            ->expects($this->never())
            ->method('generateSlugForProduct');

        $this->tierAssembler
            ->expects($this->never())
            ->method('createTierCollectionFromArrayOfTierInputs');

        $this->expectException(EntityNotFoundException::class);

        ($this->handler)($command);
    }

    public function test_it_throws_exception_when_user_cant_edit_product(): void
    {
        $productId = Uuid::v4();

        $product = $this->givenAProductExists($productId);
        $this->givenCantEditProduct($product);

        /** @var UpdateProductCommand $command */
        $command = $this->createMock(UpdateProductCommand::class);
        $command->method('getProductId')->willReturn($productId);

        $this->productRepository
            ->expects($this->never())
            ->method('save');

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $this->slugForProductsManager
            ->expects($this->never())
            ->method('generateSlugForProduct');

        $this->tierAssembler
            ->expects($this->never())
            ->method('createTierCollectionFromArrayOfTierInputs');

        $this->expectException(UnauthorizedException::class);

        ($this->handler)($command);
    }
}
