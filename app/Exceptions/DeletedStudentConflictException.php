<?php

namespace App\Exceptions;

use Exception;

class DeletedStudentConflictException extends Exception
{
    public function __construct(
        public readonly int $deletedStudentId,
        public readonly string $cpf
    ) {
        parent::__construct("Aluno com CPF {$cpf} já existe mas está excluído.", 409);
    }
}
