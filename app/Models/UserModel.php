<?php

namespace App\Models;

use App\Entities\User;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = User::class;
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'nome_completo',
        'email',
        'password_hash',
    ];

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function findByEmail(string $email): ?User
    {
        return $this->where('email', $email)->first();
    }
}
