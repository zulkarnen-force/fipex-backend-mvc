<?php

namespace App\Database\Seeds;

use App\Models\User;
use App\Repositories\AuthRepository;
use App\Services\AuthService;
use CodeIgniter\Database\Seeder;

class UserSeed extends Seeder
{
    public function run()
    {
        $authrepository = new AuthRepository(new User());
        $authService = new AuthService($authrepository);
        $data = [
            'name' => 'zulkarnen',
            'email'    => 'zulkarnen@theempire.com',
            'password'    => 'supersecret',
            'is_temporary'    => true,
            'avatar'    => 'https://google.com/img/avatar.png',
            'bio'    => 'i am superman',
        ];

        $authService->register($data);
   
    }
}

?>