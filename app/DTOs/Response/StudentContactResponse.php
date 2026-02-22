<?php

namespace App\DTOs\Response;

use App\DTOs\BaseDTO;
use App\Entities\StudentContact;

class StudentContactResponse extends BaseDTO
{
    public function __construct(
        public readonly ?string $email,
        public readonly ?string $telefones,
        public readonly ?string $rede_social
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

    public static function fromEntity(StudentContact $contact): self
    {
        return new self(
            email: $contact->email,
            telefones: $contact->telefones,
            rede_social: $contact->rede_social
        );
    }
}
