<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class StudentContact extends Entity
{
    protected $casts = [
        'id'         => 'int',
        'student_id' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $datamap = [];
}
