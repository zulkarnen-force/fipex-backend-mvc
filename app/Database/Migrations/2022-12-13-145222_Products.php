<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Products extends Migration
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
                'constraint' => '100',
                'null' => false
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'total_points' => [
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => true,
            ],
            'category_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'exhibition_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'author_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addForeignKey('author_id', 'users', 'id');
        $this->forge->addForeignKey('category_id', 'categories', 'id');
        $this->forge->addForeignKey('exhibition_id', 'exhibitions', 'id');
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
