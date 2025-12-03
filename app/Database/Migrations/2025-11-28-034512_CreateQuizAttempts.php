<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizAttempts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true, // Nullable for guest attempts
            ],
            'entri_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'chosen_answer' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'correct_answer' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'is_correct' => [
                'type'       => 'BOOLEAN',
                'null'       => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL'); // CASCADE on delete, SET NULL if user deleted
        $this->forge->addForeignKey('entri_id', 'entri', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quiz_attempts');
    }

    public function down()
    {
        $this->forge->dropTable('quiz_attempts');
    }
}
