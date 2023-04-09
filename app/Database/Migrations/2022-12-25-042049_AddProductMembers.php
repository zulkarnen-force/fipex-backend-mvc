<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductMembers extends Migration
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


        $this->forge->addForeignKey('product_id', 'products', 'id');
        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('product_members');
    }

    public function down()
    {
        $this->forge->dropTable('product_members');
    }
}
