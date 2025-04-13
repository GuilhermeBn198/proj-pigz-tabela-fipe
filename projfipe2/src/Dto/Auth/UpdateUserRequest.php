<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRequest
{
    public function __construct(
        #[Assert\Email(message: 'Formato de email inválido')]
        public readonly ?string $email = null,

        #[Assert\Length(min: 2, minMessage: 'O nome deve ter ao menos {{ limit }} caracteres')]
        public readonly ?string $name = null,

        #[Assert\Length(min: 6, minMessage: 'A senha deve ter ao menos {{ limit }} caracteres')]
        public readonly ?string $password = null,
    ) {}
}
