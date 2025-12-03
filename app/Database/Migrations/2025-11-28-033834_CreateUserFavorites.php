<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserFavorites extends Migration
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
                'null'       => false,
            ],
            'entri_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('entri_id', 'entri', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_favorites');
    }

    public function down()
    {
        $this->forge->dropTable('user_favorites');
    }
}
