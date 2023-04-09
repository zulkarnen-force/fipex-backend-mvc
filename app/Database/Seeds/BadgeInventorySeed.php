<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BadgeInventorySeed extends Seeder
{
    public function run()
    {
        $fucker = \Faker\Factory::create('id_ID');

        $data = [
            'id'       => $fucker->uuid(),
            'type' => 'gold',
            'exhibition_id'    => $fucker->uuid(),
            'user_id'    => $fucker->uuid(),
        ];

        // Simple Queries
        // $this->db->query('INSERT INTO user (username, email) VALUES(:username:, :email:)', $data);

        // Using Query Builder
        $this->db->table('badge_inventory')->insert($data);
    }
}

?>