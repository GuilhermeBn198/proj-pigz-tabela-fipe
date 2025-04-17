<?php

namespace App\Tests\Entity;

use App\Entity\Brand;
use App\Entity\Model;
use App\Enum\VehicleType;
use PHPUnit\Framework\TestCase;

class BrandTest extends TestCase
{
    public function testSetAndGetName(): void
    {
        $brand = new Brand();
        $brand->setName('Fiat');
        $this->assertEquals('Fiat', $brand->getName());
    }

    public function testSetAndGetFipeCode(): void
    {
        $brand = new Brand();
        $brand->setFipeCode('001');
        $this->assertEquals('001', $brand->getFipeCode());
    }

    public function testSetAndGetType1(): void
    {
        $brand = new Brand();
        $brand->setType(VehicleType::CARROS);
        $this->assertEquals(VehicleType::CARROS, $brand->getType());
    }

    public function testSetAndGetType2(): void
    {
        $brand = new Brand();
        $brand->setType(VehicleType::MOTOS);
        $this->assertEquals(VehicleType::MOTOS, $brand->getType());
    }
    public function testSetAndGetType3(): void
    {
        $brand = new Brand();
        $brand->setType(VehicleType::CAMINHOES);
        $this->assertEquals(VehicleType::CAMINHOES, $brand->getType());
    }
    public function testAddAndRemoveModel(): void
    {
        $brand = new Brand();
        $model = new Model();
        $brand->addModel($model);

        $this->assertTrue($brand->getModels()->contains($model));
        $this->assertEquals($brand, $model->getBrand());

        $brand->removeModel($model);
        $this->assertFalse($brand->getModels()->contains($model));
        $this->assertNull($model->getBrand());
    }

    public function testInitialIdIsNull(): void
    {
        $brand = new Brand();
        $this->assertNull($brand->getId());
    }
}
