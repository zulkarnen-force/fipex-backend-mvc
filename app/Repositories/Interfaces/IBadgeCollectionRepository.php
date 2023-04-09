<?php

namespace App\Repositories\Interfaces;

use App\Models\UserModel;
use CodeIgniter\Model;

interface IBadgeCollectionRepository
{
    public function insert($data);
    public function list();
    public function delete($id);
    public function find($id);
    public function update($id, $data);
    public function getById($id);
    public function getBadgesByProductId(string $id);
    public function countBadgesProduct(string $id);
}