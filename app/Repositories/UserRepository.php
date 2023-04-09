<?php

namespace App\Repositories;

// use CodeIgniter\Database;
use CodeIgniter\Model;

class UserRepository {

    private $model;
    private $db;
 
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->db = $model;
    }

    public function getAuthorByProductId(string $productId): array
    {
        $query = $this->model->where('product_id', $productId);
        return $query->get()->getResult();
    }
}