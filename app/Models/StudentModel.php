<?php

namespace App\Models;

use App\Entities\Student;
use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table          = 'students';
    protected $primaryKey     = 'id';
    protected $returnType     = Student::class;
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nome_completo',
        'cpf',
        'rg',
        'sexo',
        'genero',
        'foto',
    ];

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function paginatedList(int $perPage = 15, ?string $search = null): array
    {
        $builder = $this->select('id, nome_completo, cpf, created_at');

        if ($search !== null && $search !== '') {
            $builder->groupStart()
                ->like('nome_completo', $search)
                ->orLike('cpf', $search)
            ->groupEnd();
        }

        $builder->orderBy('nome_completo', 'ASC');

        return [
            'data'  => $builder->paginate($perPage),
            'pager' => $this->pager,
        ];
    }

    public function findDeletedByCpf(string $cpf): ?Student
    {
        return $this->onlyDeleted()->where('cpf', $cpf)->first();
    }
}
