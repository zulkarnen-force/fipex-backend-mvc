<?php

namespace App\Repositories\Common;

use App\Models\UserModel;
use CodeIgniter\Model;

interface RepositoryInterface
{
    public function insert(Model $user);
    public function list();
    public function delete($id);
    public function find($id);
    public function getById($id);
    public function update($id, $data);
}