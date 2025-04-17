<?php
// tests/Service/VehicleServiceTest.php
namespace App\Tests\Service;

use App\Dto\Vehicle\CreateVehicleRequest;
use App\Dto\Vehicle\TransferVehicleRequest;
use App\Dto\Vehicle\UpdateVehicleRequest;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Model;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Entity\Year;
use App\Enum\VehicleType;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Repository\ModelRepository;
use App\Repository\UserRepository;
use App\Repository\YearRepository;
use App\Service\FipeApiService;
use App\Service\VehicleService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VehicleServiceTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private $em;
    /** @var CategoryRepository&MockObject */
    private $categoryRepo;
    /** @var BrandRepository&MockObject */
    private $brandRepo;
    /** @var ModelRepository&MockObject */
    private $modelRepo;
    /** @var YearRepository&MockObject */
    private $yearRepo;
    /** @var UserRepository&MockObject */
    private $userRepo;
    /** @var FipeApiService&MockObject */
    private $fipeApi;
    /** @var VehicleService */
    private $vehicleService;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->categoryRepo = $this->createMock(CategoryRepository::class);
        $this->brandRepo = $this->createMock(BrandRepository::class);
        $this->modelRepo = $this->createMock(ModelRepository::class);
        $this->yearRepo = $this->createMock(YearRepository::class);
        $this->userRepo = $this->createMock(UserRepository::class);
        $this->fipeApi = $this->createMock(FipeApiService::class);

        $this->vehicleService = new VehicleService(
            $this->em,
            $this->categoryRepo,
            $this->brandRepo,
            $this->modelRepo,
            $this->yearRepo,
            $this->userRepo,
            $this->fipeApi
        );
    }

    public function testListAllVehicles()
    {
        $expectedVehicles = [new Vehicle(), new Vehicle()];

        /** @var \Doctrine\ORM\EntityRepository&\PHPUnit\Framework\MockObject\MockObject $vehicleRepo */
        $vehicleRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $vehicleRepo->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedVehicles);

        /** @var EntityManagerInterface&\PHPUnit\Framework\MockObject\MockObject $emMock */
        $emMock = $this->em;
        $emMock->expects($this->once())
            ->method('getRepository')
            ->with(Vehicle::class)
            ->willReturn($vehicleRepo);

        $result = $this->vehicleService->listAll();
        $this->assertSame($expectedVehicles, $result);
    }

    public function testCreateVehicleSuccessfully(): void
    {
        // Configurar o usuário dono do veículo
        $owner = (new User())
            ->setEmail('owner@example.com')
            ->setName('John Doe')
            ->setPassword('securepassword');

        // Criar DTO usando Reflection para definir propriedades readonly
        $dto = new CreateVehicleRequest();
        $reflection = new \ReflectionClass($dto);

        $properties = [
            'category' => 'carros',
            'brand' => 'brand_123',
            'model' => 'model_456',
            'yearCode' => '2024-1',
            'salePrice' => 100000.00
        ];

        foreach ($properties as $name => $value) {
            $prop = $reflection->getProperty($name);
            $prop->setAccessible(true);
            $prop->setValue($dto, $value);
        }

        // Criar entidades completas com relações
        $category = (new Category())->setName('carros');

        $brand = (new Brand())
            ->setName('Volkswagen')
            ->setFipeCode('brand_123')
            ->setType(VehicleType::CARROS); // Inicializa a propriedade type

        $model = (new Model())
            ->setName('Gol')
            ->setFipeCode('model_456')
            ->setBrand($brand);

        $year = (new Year())
            ->setName('2024')
            ->setFipeCode('2024-1')
            ->setModel($model);

        // Configurar repositórios mockados
        $this->categoryRepo->method('findOneBy')->willReturn($category);
        $this->brandRepo->method('findOneBy')->willReturn($brand);
        $this->modelRepo->method('findOneBy')->willReturn($model);
        $this->yearRepo->method('findOneBy')->willReturn($year);

        // Mock da API FIPE
        $this->fipeApi->method('getVehicleDetails')->willReturn([
            'Valor' => 'R$ 96382.00',
            'Marca' => 'VW',
            'Modelo' => 'Gol',
            'AnoModelo' => 2024,
            'Combustivel' => 'Gasolina',
            'CodigoFipe' => '123',
            'MesReferencia' => 'abril/2024',
            'SiglaCombustivel' => 'G'
        ]);

        // Configurar expectativas do EntityManager
        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Vehicle::class));

        $this->em->expects($this->once())
            ->method('flush');

        // Executar o serviço
        $vehicle = $this->vehicleService->createVehicle($owner, $dto);

        // Asserts
        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame($owner, $vehicle->getUser());
        $this->assertSame('carros', $vehicle->getCategory()->getName());
        $this->assertSame('brand_123', $vehicle->getBrand()->getFipeCode());
        $this->assertSame(VehicleType::CARROS, $vehicle->getBrand()->getType()); // Verifica o type
        $this->assertSame('model_456', $vehicle->getModel()->getFipeCode());
        $this->assertSame('2024-1', $vehicle->getYear()->getFipeCode());
        $this->assertSame('96382.00', $vehicle->getFipeValue());
        $this->assertSame(100000.00, $vehicle->getSalePrice());
        $this->assertSame('for_sale', $vehicle->getStatus());
    }

    public function testUpdateVehicleUpdatesPriceAndStatus()
    {
        $vehicle = new Vehicle();
        $vehicle->setSalePrice('90000');
        $vehicle->setStatus('for_sale');
    
        // sem named args:
        $dto = new UpdateVehicleRequest();
        $dto->salePrice = 95000.00;
        $dto->status    = 'sold';
    
        $this->em->expects($this->once())->method('flush');
    
        $updatedVehicle = $this->vehicleService->updateVehicle($vehicle, $dto);
    
        $this->assertEquals('95000',   $updatedVehicle->getSalePrice());
        $this->assertEquals('sold',    $updatedVehicle->getStatus());
    }

    public function testDeleteVehicleRemovesEntity()
    {
        $vehicle = new Vehicle();

        $this->em->expects($this->once())
            ->method('remove')
            ->with($vehicle);
        $this->em->expects($this->once())
            ->method('flush');

        $this->vehicleService->deleteVehicle($vehicle);
    }

    // public function testTransferOwnershipSuccessfully()
    // {
    //     $vehicle = new Vehicle();
    //     $oldOwner = new User();
    //     $newOwner = new User();
    //     $vehicle->setUser($oldOwner);

    //     $dto = new TransferVehicleRequest(
    //         newOwnerEmail: $newOwner->getUserIdentifier()
    //     );

    //     $this->userRepo->method('findOneBy')->willReturn($newOwner);
    //     $this->em->expects($this->once())->method('flush');

    //     $result = $this->vehicleService->transferOwnership($vehicle, $dto);

    //     $this->assertSame($newOwner, $result->getUser());
    // }

    // public function testTransferOwnershipThrowsIfUserNotFound()
    // {
    //     $this->expectException(NotFoundHttpException::class);

    //     $dto = new TransferVehicleRequest();
    //     $dto->newOwnerEmail = 'invalid@email.com';

    //     $this->userRepo->method('findOneBy')->willReturn(null);

    //     $this->vehicleService->transferOwnership(new Vehicle(), $dto);
    // }

}
