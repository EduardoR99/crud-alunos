<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Student extends Entity
{
    protected $casts = [
        'id'         => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $datamap = [];

    public array $contacts = [];
    public array $addresses = [];

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['contacts']  = $this->contacts;
        $data['addresses'] = $this->addresses;

        return $data;
    }
}
