<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExhibitions extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'date_start DATETIME',
            'date_end DATETIME',
            'scoring_start DATETIME',
            'scoring_end DATETIME',
            'register_start DATETIME',
            'register_end DATETIME',
            'image_url' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);


        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('exhibitions');
    }

    public function down()
    {
        $this->forge->dropTable('exhibitions');
    }
}
