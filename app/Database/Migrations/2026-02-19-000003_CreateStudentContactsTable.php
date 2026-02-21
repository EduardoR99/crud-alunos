<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateStudentContactsTable extends Migration
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
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'telefones' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'rede_social' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addKey('student_id', false, false, 'idx_student_contacts_student_id');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE', 'fk_contacts_student_id');
        $this->forge->createTable('student_contacts', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('student_contacts', true);
    }
}
