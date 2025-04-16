<?php

namespace App\Security\Voter;

use App\Entity\Vehicle;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VehicleVoter extends Voter
{
    public const EDIT = 'VEHICLE_EDIT';
    public const TRANSFER = 'VEHICLE_TRANSFER';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::TRANSFER], true)) {
            return false;
        }

        if (!$subject instanceof Vehicle) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $vehicle, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // Usuário anônimo não pode fazer nada
            return false;
        }

        // Administrador pode fazer todas as ações
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($vehicle, $user);
            case self::TRANSFER:
                return $this->canTransfer($vehicle, $user);
        }

        return false;
    }

    private function canEdit(Vehicle $vehicle, User $user): bool
    {
        // Apenas o dono pode editar o veículo para usuários comuns
        return $user === $vehicle->getUser();
    }

    private function canTransfer(Vehicle $vehicle, User $user): bool
    {
        // Regras para a transferência:
        // 1. O usuário que solicita não deve ser o dono atual.
        if ($user === $vehicle->getUser()) {
            return false;
        }

        // 2. O veículo deve estar com status 'for_sale'.
        if ($vehicle->getStatus() !== 'for_sale') {
            // Se o status for 'sold', verificamos se passou 1 hora desde a transação
            if ($vehicle->getStatus() === 'sold' && $vehicle->getSoldAt() !== null) {
                $interval = (new \DateTime())->getTimestamp() - $vehicle->getSoldAt()->getTimestamp();
                if ($interval < 3600) {
                    // Ainda não passou uma hora
                    return false;
                }
            } else {
                return false;
            }
        }

        //@TODO Poderiam ser inseridas validações adicionais, por exemplo: verificação de solicitação pendente.
        return true;
    }
}
