<?php

namespace App\Tests\Entity;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\Year;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testSetAndGetName(): void
    {
        $model = new Model();
        $model->setName('Uno');
        $this->assertEquals('Uno', $model->getName());
    }

    public function testSetAndGetFipeCode(): void
    {
        $model = new Model();
        $model->setFipeCode('123abc');
        $this->assertEquals('123abc', $model->getFipeCode());
    }

    public function testSetAndGetBrand(): void
    {
        $brand = new Brand();
        $model = new Model();
        $model->setBrand($brand);

        $this->assertSame($brand, $model->getBrand());
    }

    public function testAddAndRemoveYear(): void
    {
        $model = new Model();
        $year = new Year();

        $model->addYear($year);
        $this->assertTrue($model->getYears()->contains($year));
        $this->assertSame($model, $year->getModel());

        $model->removeYear($year);
        $this->assertFalse($model->getYears()->contains($year));
        $this->assertNull($year->getModel());
    }

    public function testInitialIdIsNull(): void
    {
        $model = new Model();
        $this->assertNull($model->getId());
    }
}
