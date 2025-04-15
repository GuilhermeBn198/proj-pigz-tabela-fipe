<?php
// tests/Unit/Security/Voter/VehicleVoterTest.php

namespace App\Tests\Unit\Security\Voter;

use App\Entity\User;
use App\Entity\Vehicle;
use App\Security\Voter\VehicleVoter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class VehicleVoterTest extends TestCase
{
    private VehicleVoter $voter;
    /** @var MockObject&TokenInterface */
    private $token;
    private Vehicle $vehicle;
    /** @var MockObject&User */
    private $currentUser;
    
    protected function setUp(): void
    {
        $this->voter = new VehicleVoter();
        $this->token = $this->createMock(TokenInterface::class);
        $this->vehicle = new Vehicle();
    }
    
    private function mockUser(string $email, array $roles): User
    {
        $user = new User();
        $user->setEmail($email)->setRoles($roles);
        return $user;
    }
    
    public function testAnonymousCannotAccess(): void
    {
        $this->token->method('getUser')->willReturn(null);
        $this->assertFalse($this->voter->vote($this->token, $this->vehicle, [VehicleVoter::EDIT]) > 0);
    }
    
    public function testAdminCanDoAnything(): void
    {
        $admin = $this->mockUser('a@b.com', ['ROLE_ADMIN']);
        $this->token->method('getUser')->willReturn($admin);
        
        $this->assertTrue($this->voter->vote($this->token, $this->vehicle, [VehicleVoter::EDIT]) > 0);
        $this->assertTrue($this->voter->vote($this->token, $this->vehicle, [VehicleVoter::DELETE]) > 0);
    }
    
    public function testOwnerCanEditAndDelete(): void
    {
        $owner = $this->mockUser('owner@example.com', ['ROLE_USER']);
        $this->vehicle->setUser($owner);
        $this->token->method('getUser')->willReturn($owner);
        
        $this->assertTrue($this->voter->vote($this->token, $this->vehicle, [VehicleVoter::EDIT]) > 0);
        $this->assertTrue($this->voter->vote($this->token, $this->vehicle, [VehicleVoter::DELETE]) > 0);
    }
    
    public function testNonOwnerCannotEditOrDelete(): void
    {
        $owner = $this->mockUser('owner@example.com', ['ROLE_USER']);
        $nonOwner = $this->mockUser('other@example.com', ['ROLE_USER']);
        $this->vehicle->setUser($owner);
        $this->token->method('getUser')->willReturn($nonOwner);
        
        $this->assertFalse($this->voter->vote($this->token, $this->vehicle, [VehicleVoter::EDIT]) > 0);
        $this->assertFalse($this->voter->vote($this->token, $this->vehicle, [VehicleVoter::DELETE]) > 0);
    }
    
}
