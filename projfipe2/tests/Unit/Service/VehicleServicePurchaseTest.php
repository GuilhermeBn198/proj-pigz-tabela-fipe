<?php
// tests/Service/VehicleServicePurchaseTest.php

namespace App\Tests\Service;

use App\Entity\User;
use App\Entity\Vehicle;
use App\Service\VehicleService;
use App\Repository\CategoryRepository;
use App\Repository\BrandRepository;
use App\Repository\ModelRepository;
use App\Repository\YearRepository;
use App\Repository\UserRepository;
use App\Service\FipeApiService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class VehicleServicePurchaseTest extends TestCase
{
    /* @var EntityManagerInterface&MockObject $em */
    private EntityManagerInterface $em;
    /** @var CategoryRepository&MockObject $categoryRepo */
    private CategoryRepository $categoryRepo;
    /** @var BrandRepository&MockObject $brandRepo */
    private BrandRepository $brandRepo;
    /** @var ModelRepository&MockObject $modelRepo */
    private ModelRepository $modelRepo;
    /** @var YearRepository&MockObject $yearRepo */
    private YearRepository $yearRepo;
    /** @var UserRepository&MockObject $userRepo */
    private UserRepository $userRepo;
    /** @var FipeApiService&MockObject $fipeApi */
    private FipeApiService $fipeApi;
    /** @var VehicleService $service */
    private VehicleService $service;

    protected function setUp(): void
    {
        $this->em           = $this->createMock(EntityManagerInterface::class);
        $this->categoryRepo = $this->createMock(CategoryRepository::class);
        $this->brandRepo    = $this->createMock(BrandRepository::class);
        $this->modelRepo    = $this->createMock(ModelRepository::class);
        $this->yearRepo     = $this->createMock(YearRepository::class);
        $this->userRepo     = $this->createMock(UserRepository::class);
        $this->fipeApi      = $this->createMock(FipeApiService::class);

        $this->service = new VehicleService(
            $this->em,
            $this->categoryRepo,
            $this->brandRepo,
            $this->modelRepo,
            $this->yearRepo,
            $this->userRepo,
            $this->fipeApi
        );
    }

    public function testRequestPurchaseSuccess(): void
    {
        $owner = new User();
        $buyer = new User();

        $vehicle = new Vehicle();
        $vehicle->setUser($owner)
            ->setStatus('for_sale');

        // esperamos que flush seja chamado exatamente 1 vez
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->requestPurchase($vehicle, $buyer);

        $this->assertSame('pending', $result->getStatus());
        $this->assertSame($buyer, $result->getRequestedBy());
    }

    public function testRequestPurchaseNotForSale(): void
    {
        $this->expectException(\RuntimeException::class);
        $owner = new User();
        $buyer = new User();

        $vehicle = new Vehicle();
        $vehicle->setUser($owner)
            ->setStatus('sold');

        $this->service->requestPurchase($vehicle, $buyer);
    }

    public function testRequestPurchaseByOwner(): void
    {
        $this->expectException(\RuntimeException::class);
        $owner = new User();

        $vehicle = new Vehicle();
        $vehicle->setUser($owner)
            ->setStatus('for_sale');

        // buyer == owner
        $this->service->requestPurchase($vehicle, $owner);
    }

    public function testAcceptPurchaseRequestSuccess(): void
    {
        $owner = new User();
        $buyer = new User();

        $vehicle = new Vehicle();
        $vehicle->setUser($owner)
            ->setStatus('pending');
        $vehicle->setRequestedBy($buyer);

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->acceptPurchaseRequest($vehicle);

        $this->assertSame('sold', $result->getStatus());
        $this->assertSame($buyer, $result->getUser());
        $this->assertNull($result->getRequestedBy());
        $this->assertInstanceOf(\DateTimeInterface::class, $result->getSoldAt());
    }

    public function testAcceptPurchaseRequestNoPending(): void
    {
        $this->expectException(\RuntimeException::class);

        $vehicle = new Vehicle();
        $vehicle->setStatus('for_sale');
        // sem pending nem requestedBy

        $this->service->acceptPurchaseRequest($vehicle);
    }

    public function testRejectPurchaseRequestSuccess(): void
    {
        $owner = new User();
        $buyer = new User();

        $vehicle = new Vehicle();
        $vehicle->setUser($owner)
            ->setStatus('pending');
        $vehicle->setRequestedBy($buyer);

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->rejectPurchaseRequest($vehicle);

        $this->assertSame('for_sale', $result->getStatus());
        $this->assertSame($owner, $result->getUser());
        $this->assertNull($result->getRequestedBy());
        $this->assertNull($result->getSoldAt());
    }

    public function testRejectPurchaseRequestNoPending(): void
    {
        $this->expectException(\RuntimeException::class);

        $vehicle = new Vehicle();
        $vehicle->setStatus('for_sale');

        $this->service->rejectPurchaseRequest($vehicle);
    }
}
