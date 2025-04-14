<?php
namespace App\Service;

use App\Dto\Auth\LoginRequest;
use App\Dto\Auth\RegisterUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class AuthService
{
    public function __construct(
        private UserRepository $userRepo,
        private UserPasswordHasherInterface $hasher,
        private JWTTokenManagerInterface $jwtManager,
        private EntityManagerInterface $em
    ) {}

    public function login(LoginRequest $dto): string
    {
        $user = $this->userRepo->findOneBy(['email' => $dto->email]);
        if (!$user || !$this->hasher->isPasswordValid($user, $dto->password)) {
            throw new UnauthorizedHttpException('', 'Credenciais inválidas');
        }

        return $this->jwtManager->create($user);
    }

    public function register(RegisterUserRequest $dto): string
    {
        if ($this->userRepo->findOneBy(['email' => $dto->email])) {
            throw new ConflictHttpException('Email já cadastrado');
        }

        $user = new User();
        $user->setName($dto->name)
             ->setEmail($dto->email)
             ->setPassword($this->hasher->hashPassword($user, $dto->password))
             ->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return $this->jwtManager->create($user);
    }

    public function grantAdmin(int $id): void
    {
        $user = $this->userRepo->find($id);
        if (!$user) {
            throw new \DomainException('Usuário não encontrado');
        }
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            throw new \DomainException('Usuário já é admin');
        }

        $roles = array_unique(array_merge($user->getRoles(), ['ROLE_ADMIN']));
        $user->setRoles($roles);
        $this->em->flush();
    }
}
