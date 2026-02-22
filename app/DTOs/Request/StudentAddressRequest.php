<?php

namespace App\DTOs\Request;

use App\DTOs\BaseDTO;
use App\Enums\TipoEndereco;

class StudentAddressRequest extends BaseDTO
{
    public function __construct(
        public readonly string $cep,
        public readonly string $logradouro,
        public readonly string $bairro,
        public readonly string $cidade,
        public readonly string $estado,
        public readonly string $numero,
        public readonly ?string $complemento = null,
        public readonly ?string $ponto_referencia = null,
        public readonly TipoEndereco $tipo_endereco = TipoEndereco::RESIDENCIAL
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
}
