<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeFotoColumnToLongtext extends Migration
{
    public function up(): void
    {
        $this->forge->modifyColumn('students', [
            'foto' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->modifyColumn('students', [
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
        ]);
    }
}
