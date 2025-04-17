<?php
// tests/DataFixtures/UserFixturesTest.php
namespace App\Tests\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixturesTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $fixture = new UserFixtures($this->hasher);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        $executor->execute($loader->getFixtures());
    }

    public function testUsersWereLoaded(): void
    {
        $repo = $this->em->getRepository(User::class);
        $admin = $repo->findOneBy(['email' => 'admin@example.com']);
        $this->assertNotNull($admin);
        $this->assertTrue($this->hasher->isPasswordValid($admin, 'adminpass'));
    }
}
