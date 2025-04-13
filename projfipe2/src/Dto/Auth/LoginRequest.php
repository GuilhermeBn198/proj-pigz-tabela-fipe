<?php
namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'O email não pode ficar em branco')]
        #[Assert\Email(message: 'Formato de email inválido')]
        public readonly string $email,

        #[Assert\NotBlank(message: 'A senha não pode ficar em branco')]
        public readonly string $password,
    ) {}
}
