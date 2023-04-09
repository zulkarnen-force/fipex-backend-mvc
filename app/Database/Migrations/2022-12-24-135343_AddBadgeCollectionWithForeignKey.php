<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBadgeCollectionWithForeignKey extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'unique' => true
            ],
            'badge_type' => [
                'type' => 'ENUM("platinum","gold","silver")',
                'null' => false
            ],
            'comment' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'exhibition_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'product_id' => [
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


        $this->forge->addForeignKey('exhibition_id', 'exhibitions', 'id');
        $this->forge->addForeignKey('product_id', 'products', 'id');
        $this->forge->addForeignKey('user_id', 'users', 'id');

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('badges_collection');
    }

    public function down()
    {
        $this->forge->dropTable('badges_collection');
    }
}