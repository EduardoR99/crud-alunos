<?php

namespace App\DTOs\Response;

use App\DTOs\BaseDTO;
use App\Entities\User;

class UserResponse extends BaseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome_completo,
        public readonly string $email
    ) {
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'nome_completo' => $this->nome_completo,
            'email'         => $this->email,
        ];
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->id,
            nome_completo: $user->nome_completo,
            email: $user->email
        );
    }
}
