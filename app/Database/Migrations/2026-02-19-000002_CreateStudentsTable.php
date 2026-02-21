<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateStudentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome_completo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'cpf' => [
                'type'       => 'CHAR',
                'constraint' => 14,
            ],
            'rg' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'sexo' => [
                'type'       => 'ENUM',
                'constraint' => ['M', 'F'],
                'null'       => true,
            ],
            'genero' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('cpf', 'idx_students_cpf_unique');
        $this->forge->addKey('nome_completo', false, false, 'idx_students_nome_completo');
        $this->forge->createTable('students', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('students', true);
    }
}
