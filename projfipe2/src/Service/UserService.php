<?php
// src/Service/UserService.php
namespace App\Service;

use App\Dto\Auth\UpdateUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private UserRepository $userRepo,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {}

    public function findById(int $id): User
    {
        $user = $this->userRepo->find($id);
        if (!$user) {
            throw new \DomainException('Usuário não encontrado');
        }
        return $user;
    }

    public function listAll(): array
    {
        if (!$this->userRepo->findAll()) {  
            throw new \DomainException('Nenhum usuário encontrado');
        }
        return $this->userRepo->findAll();
    }

    public function update(int $id, UpdateUserRequest $dto): void
    {
        $user = $this->findById($id);

        if ($dto->email !== null) {
            $user->setEmail($dto->email);
        }
        if ($dto->name !== null) {
            $user->setName($dto->name);
        }
        if ($dto->password !== null) {
            $user->setPassword(
                $this->hasher->hashPassword($user, $dto->password)
            );
        }

        $this->em->flush();
    }

    public function delete(int $id): void
    {
        $user = $this->findById($id);
        $this->em->remove($user);
        $this->em->flush();
    }
}
