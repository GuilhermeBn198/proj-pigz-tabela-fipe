<?php
// tests/Service/UserServiceTest.php

namespace App\Tests\Service;

use App\Dto\Auth\UpdateUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    /** @var UserRepository&MockObject */
    private $userRepo;

    /** @var EntityManagerInterface&MockObject */
    private $em;

    /** @var UserPasswordHasherInterface&MockObject */
    private $hasher;

    /** @var UserService */
    private $userService;

    protected function setUp(): void
    {
        $this->userRepo = $this->createMock(UserRepository::class);
        $this->em       = $this->createMock(EntityManagerInterface::class);
        $this->hasher   = $this->createMock(UserPasswordHasherInterface::class);

        $this->userService = new UserService(
            $this->userRepo,
            $this->em,
            $this->hasher
        );
    }

    public function testFindByIdNotFound(): void
    {
        $this->userRepo
            ->method('find')
            ->willReturn(null);

        $this->expectException(\DomainException::class);
        $this->userService->findById(42);
    }

    public function testUpdate(): void
    {
        $user = new User();
        $user->setEmail('a@b.com')->setName('Old')->setPassword('oldhash');

        $this->userRepo
            ->method('find')
            ->willReturn($user);

        $dto = new UpdateUserRequest();
        $dto->email    = 'new@b.com';
        $dto->name     = 'New';
        $dto->password = 'pass123';

        $this->hasher
            ->method('hashPassword')
            ->with($user, 'pass123')
            ->willReturn('newhash');

        $this->userService->update(1, $dto);

        $this->assertSame('new@b.com', $user->getEmail());
        $this->assertSame('New', $user->getName());
        $this->assertSame('newhash', $user->getPassword());
    }

    public function testDelete(): void
    {
        $user = new User();
        $this->userRepo
            ->method('find')
            ->willReturn($user);

        $this->userService->delete(1);
        $this->addToAssertionCount(1);
    }
}
