<?php
// src/DataFixtures/CategoryFixtures.php
namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (['carros','motos', 'caminhoes'] as $name) {
            $c = new Category();
            $c->setName($name);
            $manager->persist($c);
        }
        $manager->flush();
    }
}