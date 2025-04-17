<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testSetAndGetName(): void
    {
        $category = new Category();
        $category->setName('Carro');

        $this->assertEquals('Carro', $category->getName());
    }

    public function testInitialIdIsNull(): void
    {
        $category = new Category();
        $this->assertNull($category->getId());
    }
}
