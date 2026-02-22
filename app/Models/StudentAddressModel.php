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

    public function hardDeleteByStudentId(int $studentId): void
    {
        $this->where('student_id', $studentId)->delete(null, true);
    }

    public function deleteByStudentId(int $studentId): void
    {
        $this->where('student_id', $studentId)->delete();
    }
}
