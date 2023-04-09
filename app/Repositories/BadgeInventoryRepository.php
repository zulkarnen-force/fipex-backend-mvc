<?php

namespace App\Repositories;

// use CodeIgniter\Database;
use CodeIgniter\Model;

class BadgeInventoryRepository {

    private $model;
 
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getBadgeByUserId(string $userId): array
    {
        $query = $this->model->select('*')->where('user_id',  $userId);
        return  $query->get()->getResult();
    }
}