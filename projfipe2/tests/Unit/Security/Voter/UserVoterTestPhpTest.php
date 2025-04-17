<?php
// tests/Unit/Security/Voter/UserVoterTest.php

namespace App\Tests\Unit\Security\Voter;

use App\Entity\User;
use App\Security\Voter\UserVoter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @covers \App\Security\Voter\UserVoter
 */
class UserVoterTest extends TestCase
{
    private UserVoter $voter;

    /** @var MockObject&TokenInterface */
    private $token;

    /** @var MockObject&User */
    private $currentUser;

    /** @var User */
    private $targetUser;
    
    protected function setUp(): void
    {
        $this->voter = new UserVoter();
        $this->token = $this->createMock(TokenInterface::class);

        $this->targetUser = new User();
        $this->targetUser->setEmail('foo@example.com');
    }

    private function givenCurrentUserRoles(array $roles, string $identifier): void
    {
        // Mock da classe concreta User, que possui getUserIdentifier() e getRoles()
        $this->currentUser = $this->createMock(User::class);
        $this->currentUser
             ->method('getRoles')
             ->willReturn($roles);
        $this->currentUser
             ->method('getUserIdentifier')
             ->willReturn($identifier);

        $this->token
             ->method('getUser')
             ->willReturn($this->currentUser);
    }

    public function testAnonymousCannotEditOrDelete(): void
    {
        $this->token->method('getUser')->willReturn(null);

        $this->assertFalse(
            $this->voter->vote($this->token, $this->targetUser, [UserVoter::EDIT]) > 0
        );
        $this->assertFalse(
            $this->voter->vote($this->token, $this->targetUser, [UserVoter::DELETE]) > 0
        );
    }

    public function testAdminCanEditAndDelete(): void
    {
        $this->givenCurrentUserRoles(['ROLE_ADMIN'], 'admin@example.com');

        $this->assertTrue(
            $this->voter->vote($this->token, $this->targetUser, [UserVoter::EDIT]) > 0
        );
        $this->assertTrue(
            $this->voter->vote($this->token, $this->targetUser, [UserVoter::DELETE]) > 0
        );
    }

    public function testUserCanEditAndDeleteSelf(): void
    {
        $this->givenCurrentUserRoles(['ROLE_USER'], 'foo@example.com');
        // targetUser tem email foo@example.com
        $this->assertTrue(
            $this->voter->vote($this->token, $this->targetUser, [UserVoter::EDIT]) > 0
        );
        $this->assertTrue(
            $this->voter->vote($this->token, $this->targetUser, [UserVoter::DELETE]) > 0
        );
    }

    public function testUserCannotEditOrDeleteOther(): void
    {
        $this->givenCurrentUserRoles(['ROLE_USER'], 'other@example.com');

        $this->assertFalse(
            $this->voter->vote($this->token, $this->targetUser, [UserVoter::EDIT]) > 0
        );
        $this->assertFalse(
            $this->voter->vote($this->token, $this->targetUser, [UserVoter::DELETE]) > 0
        );
    }
}
