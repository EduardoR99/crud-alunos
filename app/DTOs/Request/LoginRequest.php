<?php

namespace App\DTOs\Request;

use App\DTOs\BaseDTO;

class LoginRequest extends BaseDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {
    }

    public function toArray(): array
    {
        return [
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }
}
