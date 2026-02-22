<?php

namespace App\DTOs\Response;

use App\DTOs\BaseDTO;
use App\Entities\Student;
use App\Enums\Sexo;
use DateTimeInterface;

class StudentResponse extends BaseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome_completo,
        public readonly string $cpf,
        public readonly ?string $rg,
        public readonly ?Sexo $sexo,
        public readonly ?string $genero,
        public readonly ?string $foto,
        public readonly array $contacts,
        public readonly array $addresses,
        public readonly DateTimeInterface $created_at
    ) {
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'nome_completo' => $this->nome_completo,
            'cpf'           => $this->cpf,
            'rg'            => $this->rg,
            'sexo'          => $this->sexo?->value,
            'genero'        => $this->genero,
            'foto'          => $this->foto,
            'contacts'      => $this->contacts,
            'addresses'     => $this->addresses,
            'created_at'    => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromEntity(Student $student): self
    {
        $contacts = array_map(
            fn($contact) => StudentContactResponse::fromEntity($contact)->toArray(),
            $student->contacts
        );

        $addresses = array_map(
            fn($address) => StudentAddressResponse::fromEntity($address)->toArray(),
            $student->addresses
        );

        return new self(
            id: $student->id,
            nome_completo: $student->nome_completo,
            cpf: $student->cpf,
            rg: $student->rg,
            sexo: Sexo::tryFromNullable($student->sexo),
            genero: $student->genero,
            foto: $student->foto,
            contacts: $contacts,
            addresses: $addresses,
            created_at: $student->created_at
        );
    }
}
