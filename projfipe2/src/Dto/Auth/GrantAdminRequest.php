<?php

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class GrantAdminRequest
{
    #[Assert\NotBlank(message: "O ID do usuário é obrigatório.")]
    #[Assert\Positive(message: "O ID do usuário deve ser um número positivo.")]
    public int $id;
}
