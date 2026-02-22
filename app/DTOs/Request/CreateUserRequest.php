<?php

namespace App\DTOs\Request;

use App\DTOs\BaseDTO;

class CreateUserRequest extends BaseDTO
{
    public function __construct(
        public readonly string $nome_completo,
        public readonly string $email,
        public readonly string $password
    ) {
    }

    public function toArray(): array
    {
        return [
            'nome_completo' => $this->nome_completo,
            'email'         => $this->email,
            'password'      => $this->password,
        ];
    }
}
