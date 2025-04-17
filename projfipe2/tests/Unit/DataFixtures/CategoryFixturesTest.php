<?php
// tests/DataFixtures/CategoryFixturesTest.php
namespace App\Tests\DataFixtures;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class CategoryFixturesTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new CategoryFixtures());

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge(); // limpa o banco
        $executor->execute($loader->getFixtures());
    }

    public function testCategoriesWereLoaded(): void
    {
        $repo = $this->em->getRepository(Category::class);
        $categories = $repo->findAll();

        $this->assertCount(3, $categories);
        $this->assertEquals('carros', $categories[0]->getName());

    }
}