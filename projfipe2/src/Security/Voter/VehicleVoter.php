<?php

use App\Entity\User;
use App\Entity\Vehicle;
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
            return false;
        }

        // ROLE_ADMIN pode fazer tudo
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
        // Apenas o dono do veículo pode editar (para usuários ROLE_USER)
        return $user === $vehicle->getUser();
    }

    private function canTransfer(Vehicle $vehicle, User $user): bool
    {
        // Para a transferência (compra):
        // 1. O comprador não pode ser o dono atual.
        // 2. O veículo deve estar com o status 'for_sale'.
        if ($user === $vehicle->getUser()) {
            return false;
        }

        if ($vehicle->getStatus() !== 'for_sale') {
            // Se o veículo estiver com status 'sold', pode haver restrição de tempo
            if ($vehicle->getStatus() === 'sold' && $vehicle->getSoldAt() !== null) {
                // Verifica se já se passou 1 hora desde a efetivação da venda
                $interval = (new \DateTime())->getTimestamp() - $vehicle->getSoldAt()->getTimestamp();
                if ($interval < 3600) {
                    return false;
                }
            } else {
                return false;
            }
        }

        // Aqui, pode-se inserir validações adicionais, por exemplo:
        // - Verificar se já existe uma solicitação pendente de aceitação do vendedor.
        // - Registrar a transação em um log ou similar.
        return true;
    }
}
