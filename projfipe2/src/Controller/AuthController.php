<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Cria e persiste o usuário
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();

        // Gera o token JWT
        $token = $jwtManager->create($user);

        return new JsonResponse(
            ['token' => $token],
            JsonResponse::HTTP_CREATED
        );
    }

    #[Route('/api/users/{id}', name: 'api_user_show', methods: ['GET'])]
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
    
    
    #[Route('/api/users/{id}', name: 'api_user_update', methods: ['PUT'])]
    public function update(int $id, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);
        
        if (!$user) {
            return new JsonResponse(['message' => 'Usuário não encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        
        if (isset($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
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
