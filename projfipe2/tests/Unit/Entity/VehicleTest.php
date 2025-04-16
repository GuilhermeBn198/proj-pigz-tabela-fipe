<?php

namespace App\Tests\Entity;

use App\Entity\Vehicle;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\Year;
use PHPUnit\Framework\TestCase;

class VehicleTest extends TestCase
{
    public function testInitialValues(): void
    {
        $vehicle = new Vehicle();

        // ID deve ser null inicialmente
        $this->assertNull($vehicle->getId());

        // Status padrão
        $this->assertEquals('for_sale', $vehicle->getStatus());
    }

    public function testSetAndGetFipeValue(): void
    {
        $vehicle = new Vehicle();
        $vehicle->setFipeValue('32000.50');

        $this->assertEquals('32000.50', $vehicle->getFipeValue());
    }

    public function testSetAndGetStatus(): void
    {
        $vehicle = new Vehicle();
        $vehicle->setStatus('sold');

        $this->assertEquals('sold', $vehicle->getStatus());
    }

    public function testSetAndGetSalePrice(): void
    {
        $vehicle = new Vehicle();
        $vehicle->setSalePrice(35000.00);

        $this->assertEquals(35000.00, $vehicle->getSalePrice());
    }

    public function testSetAndGetUser(): void
    {
        $user = new User();
        $vehicle = new Vehicle();

        $vehicle->setUser($user);
        $this->assertSame($user, $vehicle->getUser());
    }

    public function testSetAndGetCategory(): void
    {
        $category = new Category();
        $vehicle = new Vehicle();

        $vehicle->setCategory($category);
        $this->assertSame($category, $vehicle->getCategory());
    }

    public function testSetAndGetBrand(): void
    {
        $brand = new Brand();
        $vehicle = new Vehicle();

        $vehicle->setBrand($brand);
        $this->assertSame($brand, $vehicle->getBrand());
    }

    public function testSetAndGetModel(): void
    {
        $model = new Model();
        $vehicle = new Vehicle();

        $vehicle->setModel($model);
        $this->assertSame($model, $vehicle->getModel());
    }

    public function testSetAndGetYearEntity(): void
    {
        $year = new Year();
        $vehicle = new Vehicle();

        $vehicle->setYearEntity($year);
        $this->assertSame($year, $vehicle->getYearEntity());
    }
}
