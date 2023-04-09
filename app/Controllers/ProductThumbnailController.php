<?php

namespace App\Controllers;

use App\Models\ProductThumbnail;
use App\Models\Product;
use App\Services\ProductThumbnailService;
use App\Api\Domains\ProductThumbnail\Repository\SqlProductThumbnailRepository;
use CodeIgniter\RESTful\ResourceController;

class ProductThumbnailController extends ResourceController
{
    public $service;
    public function __construct() {
        $this->service = new ProductThumbnailService(new ProductThumbnail());
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
    
    
    public function saveThumbnail($productId)
    {
        try {
            $validation = \Config\Services::validation();
           $validation->setRules([
               'base64' => 'required',
               'extension' => 'required',
           ]);
           $validation->withRequest($this->request)->run();

           if (!empty($validation->getErrors())) {
               return $this->fail(
                   $validation->getErrors()
               );
           }

            $P = new Product();
           if (!$P->find($productId)) {
            return $this->respond(['message' => 'product not found'], 404);
            }
           $base64 = $this->request->getJsonVar('base64');
           $ext = $this->request->getJsonVar('extension');
           $filename = uniqid() . "." . $ext;

           $path = 'public/images/thumbnails/' . $filename;
           $url = $this->service->storeImageFromBase64($path, $base64);
           
           $response = $this->service->create(['product_id' => $productId, 'image_url' => $url]);
           return $this->respond(['message' => 'image uploaded successfully', 'path' => $url], 200);

       }    catch (\Throwable $th) {
           return $this->respond($th->getMessage(), $th->getCode());
       }

    }


}