<?php

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserRequest
{
    #[Assert\NotBlank(message: "O nome é obrigatório.")]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\NotBlank(message: "O e-mail é obrigatório.")]
    #[Assert\Email(message: "O e-mail '{{ value }}' não é válido.")]
    #[Assert\Length(max: 255)]
    public string $email;

    #[Assert\NotBlank(message: "A senha é obrigatória.")]
    #[Assert\Length(min: 6, max: 255)]
    public string $password;
}
