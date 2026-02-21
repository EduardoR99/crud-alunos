<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $casts = [
        'id'         => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $datamap = [];

    public function setPassword(string $password): static
    {
        $this->attributes['password_hash'] = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password_hash']);
    }
}
