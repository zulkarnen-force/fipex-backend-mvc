<?php

namespace App\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Models\ProductMember;
use CodeIgniter\RESTful\ResourceController;

class ProductController extends ResourceController
{
    public $service;
    public function __construct() {
        $this->service = new ProductService(new Product());
    }

    public function index()
    {
        $response = $this->service->list();

        if (!$response->isSuccess()) {
            return $this->respond($response->getResponse());
        }
        return $this->respond($response->getData());
    }

    public function create()
    {
        $requestJson = $this->request->getJson(true);
        $response = $this->service->create($requestJson);
        return $this->respond($response->getResponse(), $response->getCode());

    }

    public function show($id = null)
    {
        $response = $this->service->find($id);
        if (!$response->isSuccess()) {
            return $this->respond($response->getResponse(), $response->getCode());
        }
        return $this->respond($response->getData(), $response->getCode());

    }

    public function update($id = null)
    {
        $requset = $this->request->getJson(true);
        $response = $this->service->update($id, $requset);
        if (!$response->isSuccess()) {
            return $this->respond($response->getResponse(), $response->getCode());
        }
        return $this->respond($response->getResponse(), $response->getCode());
    }


    public function destroy($id = null)
    {
        $response = $this->service->delete($id);
        return $this->respond($response->getResponse(), $response->getCode());
    }

    // get users where author_id
    public function getProductOfUser() // deprecated
    {
        helper('jwt');
        $payload = toPayloadFromRequset($this->request);
        $userId = toPayloadFromRequset($this->request)['id'];
        $isAuthor = toPayloadFromRequset($this->request)['is_author'];
        
        if ($isAuthor) {
            $response = $this->service->getProductOfAuthor($userId);
            return $this->respond($response->getData());
        }

        // jika member, query ke product_member
        $model = new ProductMember();
        $query = $model->select('p.*')
        ->join('products p', 'p.id = product_members.product_id')
        ->where(['product_members.user_id' => $userId]);

        $result = $query->get()->getResult();

        if (!$result) {
            return $this->respond(['message' => 'product of this user not found'], 404);
        }

        return $this->respond($result);
    }

    public function getProductDetail(string $productId)
    {
        $response = $this->service->getProductsDetail($productId);
        if (!$response->isSuccess()) {
            return $this->respond($response->getResponse(), 404);
        } 
        return $this->respond($response->getData());
    }


    // TODO: please move this bussiness logic to repository and service
    public function getAuthor($product_id= null)
    {
        $p = new Product();
        try {
            $q = $p->select('u.id, u.name, u.email, u.bio, u.is_author, u.created_at, u.updated_at') -> join('users u', 'u.id = products.author_id') -> where('products.id', $product_id);
            $result = $q->get()->getResult();
            array_map(function ($res) {
                $res->is_author = (bool) $res->is_author;
            }, $result);

            return $this->respond($result);
        } catch (\Throwable $th) {
            return $this->respond($th->getMessage());
        }
    }

    
    public function getProductOfAuthor()
    {
        helper('jwt');
        $authorId = toPayloadFromRequset($this->request)['id'];
        $response = $this->service->getProductOfAuthor($authorId);
        if (!$response->isSuccess()) {
            return $this->respond($response->getResponse(), $response->getCode());
        }
        return $this->respond($response->getData(), 200);
    }
    
    public function getLeaderboardProductCategoryBased ($categoryId) {
        $result = $this->service->getBiggestPointsOfProduct($categoryId);
        return $this->respond($result->getData());
    }


    public function productsOnCategory($categoryId)
    {
        $response = $this->service->getProductCategoryBased($categoryId);
        if (!$response->isSuccess()) {
            return $this->respond($response->getResponse(), $response->getCode());
        }
        return $this->respond($response->getData(), 200);
    }


    public function checkHasDeliverBadge($userId, $productId)
    {
    }

    

}