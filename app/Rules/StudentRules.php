<?php

namespace App\Rules;

class StudentRules
{
    public static function getForCreate(): array
    {
        return [
            'student.nome_completo' => 'required|min_length[3]|max_length[255]',
            'student.cpf'           => 'required|exact_length[14]',
            'student.sexo'          => 'permit_empty|in_list[M,F]',
            'student.genero'        => 'permit_empty|max_length[50]',
            'addresses.*.cep'       => 'permit_empty|max_length[9]',
            'addresses.*.estado'    => 'permit_empty|exact_length[2]',
            'contacts.*.email'      => 'permit_empty|valid_email',
        ];
    }

    public static function getForUpdate(int $id): array
    {
        return [
            'student.nome_completo' => 'required|min_length[3]|max_length[255]',
            'student.cpf'           => "required|exact_length[14]|is_unique[students.cpf,id,{$id}]",
            'student.sexo'          => 'permit_empty|in_list[M,F]',
            'student.genero'        => 'permit_empty|max_length[50]',
            'addresses.*.cep'       => 'permit_empty|max_length[9]',
            'addresses.*.estado'    => 'permit_empty|exact_length[2]',
            'contacts.*.email'      => 'permit_empty|valid_email',
        ];
    }

    public static function get(?int $ignoreId = null): array
    {
        return $ignoreId === null
            ? self::getForCreate()
            : self::getForUpdate($ignoreId);
    }
}
