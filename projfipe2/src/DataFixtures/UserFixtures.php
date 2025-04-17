<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1) Usuário Admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setName('Admin User');
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'adminpass')
        );
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $manager->persist($admin);

        // 2) Usuário Comum 1
        $user1 = new User();
        $user1->setEmail('user1@example.com');
        $user1->setName('User One');
        $user1->setPassword(
            $this->passwordHasher->hashPassword($user1, 'user1pass')
        );
        $user1->setRoles(['ROLE_USER']);
        $manager->persist($user1);

        // 3) Usuário Comum 2
        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setName('User Two');
        $user2->setPassword(
            $this->passwordHasher->hashPassword($user2, 'user2pass')
        );
        $user2->setRoles(['ROLE_USER']);
        $manager->persist($user2);

        // Salva tudo de uma vez
        $manager->flush();
    }
}
