<?php
// src/Controller/AuthController.php

namespace App\Controller;

use App\Dto\Auth\LoginRequest;
use App\Dto\Auth\RegisterUserRequest;
use App\Dto\Auth\GrantAdminRequest;
use App\Service\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class AuthController extends AbstractController
{
    public function __construct(private AuthService $authService) {}

    #[Route('/api/login', methods:['POST'])]
    public function login(#[MapRequestPayload] LoginRequest $dto): JsonResponse
    {
        $token = $this->authService->login($dto);
        return $this->json(['token' => $token]);
    }

    #[Route('/api/register', methods:['POST'])]
    public function register(#[MapRequestPayload] RegisterUserRequest $dto): JsonResponse
    {
        $token = $this->authService->register($dto);
        return $this->json(['token' => $token], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/users/{id}/grant-admin', methods:['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function grantAdmin(#[MapRequestPayload] GrantAdminRequest $dto): JsonResponse
    {
        $this->authService->grantAdmin($dto->id);
        return $this->json(['message'=>'Usuário promovido a admin com sucesso']);
    }
}
