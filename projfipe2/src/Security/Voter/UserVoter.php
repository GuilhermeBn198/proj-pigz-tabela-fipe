<?php
// src/Security/Voter/UserVoter.php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT   = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE], true)
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $targetUser */
        $targetUser = $subject;
        $currentUser = $token->getUser();

        // Não autenticado
        if (!$currentUser instanceof UserInterface) {
            return false;
        }

        // Admins podem tudo
        if (in_array('ROLE_ADMIN', $currentUser->getRoles(), true)) {
            return true;
        }

        // Apenas o próprio usuário pode editar ou deletar
        return match ($attribute) {
            self::EDIT, self::DELETE => $currentUser->getUserIdentifier() === $targetUser->getUserIdentifier(),
            default => false,
        };
    }
}
