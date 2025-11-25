<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Command\CreateProduct;

use App\Product\Application\Assembler\TierAssembler;
use App\Product\Application\Command\CreateProduct\CreateProductCommand;
use App\Product\Application\Command\CreateProduct\CreateProductCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Application\Service\ShortIdGeneratorInterface;
use App\Product\Application\Service\SlugForProductsManager;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Event\ProductCreated;
use App\Product\Domain\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\ValueObject\PublicationStatus;
use App\Product\Domain\ValueObject\TierCollection;
use App\Shared\Infrastructure\Request\Input\MoneyInput;
use App\Tests\Factories\Product\Domain\Entity\ProductFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

class CreateProductCommandHandlerTest extends UnitTestCase
{
    private ProductRepositoryInterface|MockObject $productRepositoryMock;
    private ProductAuthorizationServiceInterface|MockObject $productAuthorizationServiceMock;
    private TierAssembler|MockObject $tierAssemblerMock;
    private SlugForProductsManager|MockObject $slugForProductsManagerMock;
    private ShortIdGeneratorInterface|MockObject $shortIdGeneratorMock;
    private EventDispatcherInterface|MockObject $eventDispatcherMock;
    private CreateProductCommandHandler $createProductCommandHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->productAuthorizationServiceMock = $this->createMock(ProductAuthorizationServiceInterface::class);
        $this->tierAssemblerMock = $this->createMock(TierAssembler::class);
        $this->slugForProductsManagerMock = $this->createMock(SlugForProductsManager::class);
        $this->shortIdGeneratorMock = $this->createMock(ShortIdGeneratorInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->createProductCommandHandler = new CreateProductCommandHandler(
            $this->productRepositoryMock,
            $this->productAuthorizationServiceMock,
            $this->tierAssemblerMock,
            $this->slugForProductsManagerMock,
            $this->shortIdGeneratorMock,
            $this->eventDispatcherMock
        );
    }

    public function test_product_is_created_assigned_to_user(): void
    {
        $productId = Uuid::v4();
        $userId = Uuid::v4();

        /** @var Product $product */
        $product = ProductFactory::createOne([
            'productId'         => $productId,
            'companyId'         => null,
            'userId'            => null,
            'publicationStatus' => PublicationStatus::default(),
        ]);

        $this->givenAShortIdGenerated($product->getShortId());
        $this->givenASlugIsGenerated($product->getName(), $product->getShortId(), $product->getSlug());
        $this->givenISaveTheProduct($product);
        $this->givenOwnershipAssignment($product, $product->getCompanyId(), $userId);
        $tierCollection = $this->givenATierCollectionIsCreated($product->getTiers()->toArray());
        $this->givenIDispatchTheEventProductCreated($productId);

        $command = new CreateProductCommand(
            $productId,
            $product->getName(),
            $product->getInternalId(),
            $product->getDescription(),
            new MoneyInput($product->getDeposit()->getAmount(), $product->getDeposit()->getCurrency()),
            $tierCollection->toArray(),
            $product->getQuantity(),
            $product->getCategoryId(),
            $product->getImageIds(),
            null
        );

        $this->createProductCommandHandler->__invoke($command);
    }

    public function test_product_is_created_assigned_to_a_company(): void
    {
        $productId = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var Product $product */
        $product = ProductFactory::createOne([
            'productId'         => $productId,
            'companyId'         => null,
            'userId'            => null,
            'publicationStatus' => PublicationStatus::default(),
        ]);

        $this->givenAShortIdGenerated($product->getShortId());
        $this->givenASlugIsGenerated($product->getName(), $product->getShortId(), $product->getSlug());
        $this->givenISaveTheProduct($product);
        $this->givenOwnershipAssignment($product, $companyId);
        $tierCollection = $this->givenATierCollectionIsCreated($product->getTiers()->toArray());
        $this->givenIDispatchTheEventProductCreated($productId);

        $command = new CreateProductCommand(
            $productId,
            $product->getName(),
            $product->getInternalId(),
            $product->getDescription(),
            new MoneyInput($product->getDeposit()->getAmount(), $product->getDeposit()->getCurrency()),
            $tierCollection->toArray(),
            $product->getQuantity(),
            $product->getCategoryId(),
            $product->getImageIds(),
            $companyId
        );

        $this->createProductCommandHandler->__invoke($command);
    }

    private function givenAShortIdGenerated(string $shortId): void
    {
        $this->shortIdGeneratorMock->expects($this->once())
            ->method('generateShortId')
            ->willReturn($shortId);
    }

    private function givenASlugIsGenerated(string $name, string $shortId, string $slug): void
    {
        $this->slugForProductsManagerMock->expects($this->once())
            ->method('generateSlugForProduct')
            ->with($name, $shortId)
            ->willReturn($slug);
    }

    private function givenATierCollectionIsCreated(array $tierInputs): TierCollection
    {
        $collection = TierCollection::fromArray($tierInputs);

        $this->tierAssemblerMock->expects($this->once())
            ->method('createTierCollectionFromArrayOfTierInputs')
            ->with($tierInputs)
            ->willReturn($collection);

        return $collection;
    }

    private function givenOwnershipAssignment(Product $productCreated, ?Uuid $companyId = null, ?Uuid $userId = null): void
    {
        $this->productAuthorizationServiceMock->expects($this->once())
            ->method('assignOwnership')
            ->with(
                $this->callback(function (Product $product) use ($productCreated): bool {
                    return $productCreated->getId()->equals($product->getId());
                }),
                $companyId
            );

        if ($userId !== null) {
            $productCreated->setUserId($userId);
        }
        if ($companyId !== null) {
            $productCreated->setCompanyId($companyId);
        }
    }

    private function givenISaveTheProduct(Product $productCreated): void
    {
        $this->productRepositoryMock->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Product $product) use ($productCreated): bool {
                    return $productCreated->getId()->equals($product->getId());
                })
            );
    }

    private function givenIDispatchTheEventProductCreated(Uuid $productId): void
    {
        $this->eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(new ProductCreated($productId));
    }
}
