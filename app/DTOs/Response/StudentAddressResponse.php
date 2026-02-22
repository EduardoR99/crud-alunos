<?php

namespace App\DTOs\Response;

use App\DTOs\BaseDTO;
use App\Entities\StudentAddress;
use App\Enums\TipoEndereco;

class StudentAddressResponse extends BaseDTO
{
    public function __construct(
        public readonly string $cep,
        public readonly string $logradouro,
        public readonly string $bairro,
        public readonly string $cidade,
        public readonly string $estado,
        public readonly string $numero,
        public readonly ?string $complemento,
        public readonly ?string $ponto_referencia,
        public readonly TipoEndereco $tipo_endereco
    ) {
    }

    public function toArray(): array
    {
        return [
            'cep'               => $this->cep,
            'logradouro'        => $this->logradouro,
            'bairro'            => $this->bairro,
            'cidade'            => $this->cidade,
            'estado'            => $this->estado,
            'numero'            => $this->numero,
            'complemento'       => $this->complemento,
            'ponto_referencia'  => $this->ponto_referencia,
            'tipo_endereco'     => $this->tipo_endereco->value,
        ];
    }

    public static function fromEntity(StudentAddress $address): self
    {
        return new self(
            cep: $address->cep,
            logradouro: $address->logradouro,
            bairro: $address->bairro,
            cidade: $address->cidade,
            estado: $address->estado,
            numero: $address->numero,
            complemento: $address->complemento,
            ponto_referencia: $address->ponto_referencia,
            tipo_endereco: TipoEndereco::from($address->tipo_endereco)
        );
    }
}
