<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBadgeInventories extends Migration
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
            'badge_type' => [
                'type' => 'ENUM("platinum","gold","silver")',
                'default' => 'silver',
                'null' => false
            ],
            'badge_count' => [
                'type' => 'INT',
                'constraint' => '10',
                'default' => 100,
            ],
            'exhibition_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->addForeignKey('exhibition_id', 'exhibitions', 'id');

        $this->forge->addPrimaryKey('id');

        $this->forge->createTable('badge_inventories');
    }

    public function down()
    {
        $this->forge->dropTable('badge_inventories');
    }
}
