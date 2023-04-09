<?php

namespace App\Repositories;

// use CodeIgniter\Database;
use CodeIgniter\Model;

class ProductRepository {

    private $model;
 
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAuthorByProductId(string $productId): array
    {
        $query = $this->model->select('*')->join('users', 'users.id = author_id');
        return  $query->get()->getResult();
    }
}