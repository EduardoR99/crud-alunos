<?php

namespace App\Models;

use App\Entities\StudentAddress;
use CodeIgniter\Model;

class StudentAddressModel extends Model
{
    protected $table          = 'student_addresses';
    protected $primaryKey     = 'id';
    protected $returnType     = StudentAddress::class;
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'student_id',
        'cep',
        'logradouro',
        'bairro',
        'cidade',
        'estado',
        'numero',
        'complemento',
        'ponto_referencia',
        'tipo_endereco',
    ];

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Busca endereços em lote por IDs de alunos (prevenção N+1).
     *
     * @param int[] $studentIds
     * @return array<int, StudentAddress[]>
     */
    public function findByStudentIds(array $studentIds): array
    {
        if (empty($studentIds)) {
            return [];
        }

        $addresses = $this->whereIn('student_id', $studentIds)->findAll();

        $grouped = [];
        foreach ($addresses as $address) {
            $grouped[$address->student_id][] = $address;
        }

        return $grouped;
    }

    /**
     * Hard delete dos endereços de um aluno.
     * Usado na estratégia de update (delete + re-insert),
     * evitando acumular registros soft-deleted.
     */
    public function hardDeleteByStudentId(int $studentId): void
    {
        $this->where('student_id', $studentId)->delete(null, true);
    }

    /**
     * Soft delete dos endereços de um aluno.
     */
    public function deleteByStudentId(int $studentId): void
    {
        $this->where('student_id', $studentId)->delete();
    }
}
