<?php

namespace App\Controller;

use App\Dto\Auth\GrantAdminRequest;
use App\Dto\Auth\RegisterUserRequest;
use App\Dto\Auth\UpdateUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class AuthController extends AbstractController
{
    #[Route('/api/register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegisterUserRequest $dto,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        JWTTokenManagerInterface $jwtManager,
        UserRepository $userRepo
    ): JsonResponse {

        if ($userRepo->findOneBy(['email' => $dto->email])) {
            return new JsonResponse(['message' => 'Email já cadastrado'], JsonResponse::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setEmail($dto->email);
        $user->setName($dto->name);
        $user->setPassword($passwordHasher->hashPassword($user, $dto->password));
        $user->setRoles(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        $token = $jwtManager->create($user);
        return new JsonResponse(['token' => $token], JsonResponse::HTTP_CREATED);
    }


    #[Route('/api/users/{id}/grant-admin', name: 'api_grant_admin', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function grantAdmin(
        #[MapRequestPayload] GrantAdminRequest $dto,
        UserRepository $userRepo,
        EntityManagerInterface $em
        ): JsonResponse {
            $user = $userRepo->find($dto->id);
            if (!$user) {
                return new JsonResponse(['message' => 'Usuário não encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }

            $roles = $user->getRoles();
            if (in_array('ROLE_ADMIN', $roles)) {
                return new JsonResponse(['message' => 'Usuário já é admin'], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Adiciona ROLE_ADMIN sem remover roles existentes
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles(array_unique($roles));

            $em->flush();

            return new JsonResponse(['message' => 'Usuário promovido a admin com sucesso'], JsonResponse::HTTP_OK);
        }

    #[Route('/api/users/{id}', name: 'api_user_findById', methods: ['GET'])]
    public function show(int $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'Usuário não encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/users', name: 'api_user_list', methods: ['GET'])]
    public function showAll(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        $userList = [];
        foreach ($users as $user) {
            $userList[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }

        return new JsonResponse($userList, JsonResponse::HTTP_OK);
    }

    #[Route('/api/users/{id}', name: 'api_user_update', methods: ['PATCH'])]
    public function update(
        int $id,
        #[MapRequestPayload] UpdateUserRequest $dto,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $userRepository->find($id);
        if (!$user) {
            return new JsonResponse(['message' => 'Usuário não encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Usa o DTO validado
        if ($dto->email !== null) {
            $user->setEmail($dto->email);
        }
        if ($dto->name !== null) {
            $user->setName($dto->name);
        }
        if ($dto->password !== null) {
            $user->setPassword(
                $passwordHasher->hashPassword($user, $dto->password)
            );
        }

        $em->flush();
        return new JsonResponse(['message' => 'Usuário atualizado com sucesso'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/users/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    public function delete(int $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'Usuário não encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return new JsonResponse(['message' => 'Usuário excluído com sucesso'], JsonResponse::HTTP_NO_CONTENT);
    }
}
