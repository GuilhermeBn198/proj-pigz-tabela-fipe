<?php

namespace App\Security\Voter;

use App\Entity\Vehicle;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VehicleVoter extends Voter
{
    // Defina os atributos (ações) que o voter suportará
    public const EDIT   = 'VEHICLE_EDIT';
    public const DELETE = 'VEHICLE_DELETE';
    public const VIEW   = 'VEHICLE_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Se o atributo não for um dos que suportamos ou o subject não for Vehicle, retorna false.
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])
            && $subject instanceof Vehicle;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Pega o usuário autenticado
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            // Usuário não autenticado
            return false;
        }

        /** @var Vehicle $vehicle */
        $vehicle = $subject;

        // Caso o usuário seja admin, pode fazer qualquer coisa
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // Para EDIT e DELETE, somente o proprietário do veículo pode alterar/excluir
        switch ($attribute) {
            case self::EDIT: 
                return $vehicle->getUser()?->getUserIdentifier() === $user->getUserIdentifier();
            case self::DELETE:
                return $vehicle->getUser()?->getUserIdentifier() === $user->getUserIdentifier();

            case self::VIEW:
                return true; // Todos podem ver os veículos
        }

        return false;
    }
}
