<?php

namespace App\DTOs\Request;

use App\DTOs\BaseDTO;
use App\Enums\Sexo;

class CreateStudentRequest extends BaseDTO
{
    public function __construct(
        public readonly string $nome_completo,
        public readonly string $cpf,
        public readonly ?string $rg = null,
        public readonly ?Sexo $sexo = null,
        public readonly ?string $genero = null,
        public readonly ?string $foto = null,
        public readonly array $contacts = [],
        public readonly array $addresses = []
    ) {
    }

    public function toArray(): array
    {
        return [
            'nome_completo' => $this->nome_completo,
            'cpf'           => $this->cpf,
            'rg'            => $this->rg,
            'sexo'          => $this->sexo?->value,
            'genero'        => $this->genero,
            'foto'          => $this->foto,
            'contacts'      => $this->contacts,
            'addresses'     => $this->addresses,
        ];
    }
}
