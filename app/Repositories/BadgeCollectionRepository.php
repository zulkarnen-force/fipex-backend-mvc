<?php

namespace App\Repositories;

use App\Repositories\Common\CommonRepository;
use App\Repositories\Interfaces\IBadgeCollectionRepository;
use CodeIgniter\Model;

class BadgeCollectionRepository extends CommonRepository implements IBadgeCollectionRepository {


    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function getBadgesByProductId(string $productId)
    {
        $query = $this->model
            ->select("badges_collection.*, users.name, users.image_url as avatar, users.bio")
            ->join('users', 'users.id = badges_collection.user_id')
            ->where('badges_collection.product_id', $productId);
        $result = $query->get()->getResult();
        return $result;
        
    }

    public function countBadgesProduct(string $productId)
    {
        $query = $this->model
            ->select("badge_type as type, count(*) as total")
            ->where('badges_collection.product_id', $productId)->groupBy("badge_type");
        $result = $query->get()->getResult();
        return $result;
    }

}