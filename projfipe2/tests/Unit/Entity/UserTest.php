<?php
// tests/Unit/Entity/UserTest.php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Entity\Vehicle;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $user = new User();
        $user->setEmail('u@e.com');
        $user->setName('Nome');
        $user->setPassword('pass');
        $user->setRoles(['ROLE_USER']);

        $this->assertSame('u@e.com', $user->getEmail());
        $this->assertSame('Nome', $user->getName());
        $this->assertSame('pass', $user->getPassword());
        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }

    public function testUserIdentifierAndUsername(): void
    {
        $user = new User();
        $user->setEmail('id@example.com');
        $this->assertSame('id@example.com', $user->getUserIdentifier());
        $this->assertSame('id@example.com', $user->getUsername());
    }

    public function testVehicleCollectionManagement(): void
    {
        $user = new User();
        $vehicle = new Vehicle();
        $vehicle->setUser($user);

        // addVehicle
        $user->addVehicle($vehicle);
        $this->assertCount(1, $user->getVehicles());
        $this->assertSame($user, $vehicle->getUser());

        // removeVehicle
        $user->removeVehicle($vehicle);
        $this->assertCount(0, $user->getVehicles());
        $this->assertNull($vehicle->getUser());
    }
}
