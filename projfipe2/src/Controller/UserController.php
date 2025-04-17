<?php

namespace App\Controller;

use App\Dto\Auth\UpdateUserRequest;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    public function __construct(private UserService $userService) {}
    
    #[Route('/api/users', name: 'api_user_getAll', methods:['GET'])]
    public function listAll(): JsonResponse
    {
        $users = $this->userService->listAll();
        return $this->json($users, JsonResponse::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('/api/users/{id}', name: 'api_user_getById', methods:['GET'])]
    public function getById(int $id): JsonResponse
    {
        $user = $this->userService->findById($id);
        return $this->json($user, JsonResponse::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('/api/users/{id}', name: 'api_user_update', methods:['PUT'])]
    #[IsGranted('USER_EDIT', subject: 'user')]
    public function update(
        User $user,
        #[MapRequestPayload] UpdateUserRequest $dto
    ): JsonResponse {
        $this->userService->update($user->getId(), $dto);
        return $this->json(['message' => 'Usuário atualizado com sucesso']);
    }

    #[Route('/api/users/{id}', name: 'api_user_delete', methods:['DELETE'])]
    #[IsGranted('USER_DELETE', subject: 'user')]
    public function delete(User $user): JsonResponse
    {
        $this->userService->delete($user->getId());
        return $this->json(['message' => 'Usuário deletado com sucesso'], JsonResponse::HTTP_NO_CONTENT);
    }
}
