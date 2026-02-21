<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateStudentAddressesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'student_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'cep' => [
                'type'       => 'CHAR',
                'constraint' => 9,
            ],
            'logradouro' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'bairro' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'cidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'estado' => [
                'type'       => 'CHAR',
                'constraint' => 2,
            ],
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'complemento' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'ponto_referencia' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'tipo_endereco' => [
                'type'       => 'ENUM',
                'constraint' => ['residencial', 'comercial', 'outro'],
                'default'    => 'residencial',
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
        $this->forge->addKey('student_id', false, false, 'idx_student_addresses_student_id');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE', 'fk_addresses_student_id');
        $this->forge->createTable('student_addresses', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('student_addresses', true);
    }
}
