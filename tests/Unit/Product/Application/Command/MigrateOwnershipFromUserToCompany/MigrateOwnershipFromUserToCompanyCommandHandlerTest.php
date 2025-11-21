<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Application\Command\MigrateOwnershipFromUserToCompany;

use App\Product\Application\Command\MigrateOwnershipFromUserToCompany\MigrateOwnershipFromUserToCompanyCommand;
use App\Product\Application\Command\MigrateOwnershipFromUserToCompany\MigrateOwnershipFromUserToCompanyCommandHandler;
use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Product\Domain\Entity\Product;
use App\Tests\Factories\Product\Domain\Entity\ProductFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class MigrateOwnershipFromUserToCompanyCommandHandlerTest extends UnitTestCase
{
    /** @var ProductRepositoryInterface|MockObject */
    private ProductRepositoryInterface|MockObject $productRepositoryMock;

    private MigrateOwnershipFromUserToCompanyCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);

        $this->handler = new MigrateOwnershipFromUserToCompanyCommandHandler(
            $this->productRepositoryMock,
        );
    }

    public function test_it_migrates_ownership_from_user_to_company_for_all_products(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        /** @var Product $product1 */
        $product1 = ProductFactory::createOne([
            'userId'    => $userId,
            'companyId' => null,
        ]);

        /** @var Product $product2 */
        $product2 = ProductFactory::createOne([
            'userId'    => $userId,
            'companyId' => null,
        ]);

        $products = [$product1, $product2];

        // getProductsByUserId debe ser llamado una vez con el userId del comando
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('getProductsByUserId')
            ->with($userId)
            ->willReturn($products);

        // save debe ser llamado exactamente dos veces, con los productos ya migrados a la company
        $this->productRepositoryMock
            ->expects($this->exactly(2))
            ->method('save')
            ->with($this->callback(function (Product $product) use ($companyId): bool {
                // Comprobamos que la compañía se ha asignado correctamente al producto
                return $product->getCompanyId() !== null
                    && $product->getCompanyId()->equals($companyId);
            }));

        $command = new MigrateOwnershipFromUserToCompanyCommand(
            $userId,
            $companyId
        );

        // Ejecutamos el handler
        ($this->handler)($command);

        // A mayores, verificamos que en memoria también se ha actualizado
        $this->assertTrue($companyId->equals($product1->getCompanyId()));
        $this->assertTrue($companyId->equals($product2->getCompanyId()));
    }

    public function test_it_does_nothing_when_user_has_no_products(): void
    {
        $userId    = Uuid::v4();
        $companyId = Uuid::v4();

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('getProductsByUserId')
            ->with($userId)
            ->willReturn([]);

        // Si no hay productos, no se debería llamar a save
        $this->productRepositoryMock
            ->expects($this->never())
            ->method('save');

        $command = new MigrateOwnershipFromUserToCompanyCommand(
            $userId,
            $companyId
        );

        ($this->handler)($command);
    }
}
