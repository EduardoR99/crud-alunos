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

    protected $validationRules = [
        'email'        => 'permit_empty|valid_email|max_length[255]',
        'telefones'    => 'permit_empty|regex_match[/^\(\d{2}\)\s?\d{4,5}-?\d{4}$/]|max_length[255]',
        'rede_social'  => 'permit_empty|regex_match[/^@?[a-zA-Z0-9._]{1,30}$/]|max_length[255]',
    ];

    protected $validationMessages = [
        'telefones' => [
            'regex_match' => 'Telefone inválido (formato: (00) 00000-0000 ou (00) 0000-0000)',
        ],
        'rede_social' => [
            'regex_match' => 'Usuário de rede social inválido (apenas letras, números, . e _)',
        ],
    ];

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

    public function hardDeleteByStudentId(int $studentId): void
    {
        $this->where('student_id', $studentId)->delete(null, true);
    }

    public function deleteByStudentId(int $studentId): void
    {
        $this->where('student_id', $studentId)->delete();
    }
}
