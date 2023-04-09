<?php

namespace App\Controllers;

use App\Services\CategoryService;
use App\Models\Category;
use CodeIgniter\RESTful\ResourceController;

class CategoryController extends ResourceController
{
    public $service;
    public function __construct() {
        $this->service = new CategoryService(new Category());
    }

    public function index()
    {
        $products = $this->service->list();
        return $this->respond($products->getData());
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
        return $this->respond($response->getResponse(), $response->getCode());
    }


    public function destroy($id = null)
    {
        $response = $this->service->delete($id);
        return $this->respond($response->getResponse(), $response->getCode());
    }

    function getCategoriesOfExhibition($exhibitionId)
    {
        $r = $this->service->getCategoriesByExhibition($exhibitionId);
        return $this->respond($r);
    }
}