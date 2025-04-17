<?php
// tests/Service/AuthServiceTest.php

namespace App\Tests\Service;

use App\Dto\Auth\LoginRequest;
use App\Dto\Auth\RegisterUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class AuthServiceTest extends TestCase
{
    /** @var UserRepository&MockObject */
    private $userRepo;

    /** @var UserPasswordHasherInterface&MockObject */
    private $hasher;

    /** @var JWTTokenManagerInterface&MockObject */
    private $jwtManager;

    /** @var EntityManagerInterface&MockObject */
    private $em;

    /** @var AuthService */
    private $authService;

    protected function setUp(): void
    {
        $this->userRepo    = $this->createMock(UserRepository::class);
        $this->hasher      = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtManager  = $this->createMock(JWTTokenManagerInterface::class);
        $this->em          = $this->createMock(EntityManagerInterface::class);

        $this->authService = new AuthService(
            $this->userRepo,
            $this->hasher,
            $this->jwtManager,
            $this->em
        );
    }

    public function testLoginSuccess(): void
    {
        $dto = new LoginRequest();
        $dto->email = 'user@example.com';
        $dto->password = 'plain';

        $user = new User();
        $user->setEmail('user@example.com');

        $this->userRepo
            ->method('findOneBy')
            ->with(['email' => 'user@example.com'])
            ->willReturn($user);

        $this->hasher
            ->method('isPasswordValid')
            ->with($user, 'plain')
            ->willReturn(true);

        $this->jwtManager
            ->method('create')
            ->with($user)
            ->willReturn('token123');

        $token = $this->authService->login($dto);
        $this->assertSame('token123', $token);
    }

    public function testLoginInvalidCredentials(): void
    {
        $dto = new LoginRequest();
        $dto->email = 'user@example.com';
        $dto->password = 'plain';
        
        $this->userRepo
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(UnauthorizedHttpException::class);
        $this->authService->login($dto);
    }

    public function testRegisterSuccess(): void
    {
        // Criação do DTO sem argumentos, setando as propriedades manualmente:
        $dto = new RegisterUserRequest();
        $dto->name     = 'Name';
        $dto->email    = 'new@example.com';
        $dto->password = 'secret';

        $this->userRepo
            ->method('findOneBy')
            ->with(['email' => 'new@example.com'])
            ->willReturn(null);

        $this->hasher
            ->method('hashPassword')
            ->willReturn('hashed');

        $this->jwtManager
            ->method('create')
            ->willReturn('tokenXYZ');

        $token = $this->authService->register($dto);
        $this->assertSame('tokenXYZ', $token);
    }

    public function testRegisterConflict(): void
    {
        $dto = new RegisterUserRequest();
        $dto->name     = 'Name';
        $dto->email    = 'dup@example.com';
        $dto->password = 'secret';

        $this->userRepo
            ->method('findOneBy')
            ->willReturn(new User());

        $this->expectException(ConflictHttpException::class);
        $this->authService->register($dto);
    }
}
