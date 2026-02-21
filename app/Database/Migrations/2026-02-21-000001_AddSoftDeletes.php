<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeletes extends Migration
{
    private array $tables = ['students', 'student_contacts', 'student_addresses'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            $this->forge->addColumn($table, [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            $this->forge->dropColumn($table, 'deleted_at');
        }
    }
}
