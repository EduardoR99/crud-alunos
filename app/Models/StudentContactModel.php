<?php

namespace App\Models;

use App\Entities\StudentContact;
use CodeIgniter\Model;

class StudentContactModel extends Model
{
    protected $table          = 'student_contacts';
    protected $primaryKey     = 'id';
    protected $returnType     = StudentContact::class;
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'student_id',
        'email',
        'telefones',
        'rede_social',
    ];

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Busca contatos em lote por IDs de alunos (prevenção N+1).
     *
     * @param int[] $studentIds
     * @return array<int, StudentContact[]>
     */
    public function findByStudentIds(array $studentIds): array
    {
        if (empty($studentIds)) {
            return [];
        }

        $contacts = $this->whereIn('student_id', $studentIds)->findAll();

        $grouped = [];
        foreach ($contacts as $contact) {
            $grouped[$contact->student_id][] = $contact;
        }

        return $grouped;
    }

    /**
     * Hard delete dos contatos de um aluno.
     * Usado na estratégia de update (delete + re-insert),
     * evitando acumular registros soft-deleted.
     */
    public function hardDeleteByStudentId(int $studentId): void
    {
        $this->where('student_id', $studentId)->delete(null, true);
    }

    /**
     * Soft delete dos contatos de um aluno.
     */
    public function deleteByStudentId(int $studentId): void
    {
        $this->where('student_id', $studentId)->delete();
    }
}
