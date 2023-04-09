<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGuestBook extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null'  => false,
                'unique' => true
            ],
            'comment' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'exhibition_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('guest_books');
    }

    public function down()
    {
        $this->forge->dropTable('guest_books');
    }
}
