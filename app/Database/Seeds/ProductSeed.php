<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeed extends Seeder
{
    public function run()
    {
        $data = [
            'id'       => 'jasljdlasj',
            'name' => 'fipex apps',
            'description'    => 'this fipex apps for manage fipex exb',
            'exhibitionid_id'    => 'ex-sdsd',
            'author_id'    => 'auth-asdjas',
            'total_points'    => 100,
        ];

        // Simple Queries
        // $this->db->query('INSERT INTO user (username, email) VALUES(:username:, :email:)', $data);

        // Using Query Builder
        $this->db->table('product')->insert($data);
    }
}

?>