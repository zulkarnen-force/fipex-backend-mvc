<?php

namespace App\Repositories;

// use CodeIgniter\Database;
use CodeIgniter\Model;

class ProductMemberRepository {

    private $model;
    private $db;
 
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->db = $model;
    }

    public function getMemberByProductId(string $productId): array
    {
        $query = $this->db->select('usr.name, usr.image_url, usr.bio, usr.email, usr.id')->join('users usr', 'usr.id = user_id')->where('product_id', $productId);
        return $query->get()->getResult();
    }

    
}