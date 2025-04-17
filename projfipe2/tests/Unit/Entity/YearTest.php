<?php

namespace App\Tests\Entity;

use App\Entity\Model;
use App\Entity\Year;
use PHPUnit\Framework\TestCase;

class YearTest extends TestCase
{
    public function testSetAndGetName(): void
    {
        $year = new Year();
        $year->setName('2022');

        $this->assertEquals('2022', $year->getName());
    }

    public function testSetAndGetFipeCode(): void
    {
        $year = new Year();
        $year->setFipeCode('2022abc');

        $this->assertEquals('2022abc', $year->getFipeCode());
    }

    public function testSetAndGetModel(): void
    {
        $model = new Model();
        $year = new Year();
        $year->setModel($model);

        $this->assertSame($model, $year->getModel());
    }

    public function testInitialIdIsNull(): void
    {
        $year = new Year();
        $this->assertNull($year->getId());
    }
}
