<?php

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRequest
{
    #[Assert\Length(max: 255)]
    public ?string $name = null;

    #[Assert\Email(message: "O e-mail '{{ value }}' não é válido.")]
    #[Assert\Length(max: 255)]
    public ?string $email = null;

    #[Assert\Length(min: 6, max: 255)]
    public ?string $password = null;
}
