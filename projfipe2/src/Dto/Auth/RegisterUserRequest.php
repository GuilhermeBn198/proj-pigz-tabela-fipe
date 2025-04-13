<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'O nome não pode ficar em branco')]
        public readonly string $name,

        #[Assert\NotBlank(message: 'O email não pode ficar em branco')]
        #[Assert\Email(message: 'Formato de email inválido')]
        public readonly string $email,

        #[Assert\NotBlank(message: 'A senha não pode ficar em branco')]
        #[Assert\Length(min: 6, minMessage: 'A senha deve ter ao menos {{ limit }} caracteres')]
        public readonly string $password,
    ) {}
}
