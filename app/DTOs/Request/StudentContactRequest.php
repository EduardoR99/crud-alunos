<?php

namespace App\DTOs\Request;

use App\DTOs\BaseDTO;

class StudentContactRequest extends BaseDTO
{
    public function __construct(
        public readonly ?string $email = null,
        public readonly ?string $telefones = null,
        public readonly ?string $rede_social = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'email'        => $this->email,
            'telefones'    => $this->telefones,
            'rede_social'  => $this->rede_social,
        ];
    }
}
