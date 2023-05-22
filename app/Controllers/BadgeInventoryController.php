<?php

namespace App\Controllers;

use App\Api\Domains\BadgeCollection\Model\BadgeCollection;
use App\Models\BadgeInventory;
use App\Services\BadgeInventoryService;
use CodeIgniter\RESTful\ResourceController;

class BadgeInventoryController extends ResourceController
{
    public $service;
    public function __construct() {
        $this->service = new BadgeInventoryService(new BadgeInventory());
    }

    public function index()
    {
        $products = $this->service->list();
        return $this->respond($products->getData());
    }

    public function create()
    {
        $requestJson = $this->request->getJson(true);

        $validation = \Config\Services::validation();

        $validation->setRules([
        ]);

        $validation->withRequest($this->request)->run();
        
        if (!empty($validation->getErrors())) {     
            return $this->fail(
                $validation->getErrors()
            );
        }

        // u4d1960
        // s1f45t
        $secret = $this->request->getJsonVar('secret');
        switch ($secret) {
            case 'u4d1960':
              $requestJson['badge_type'] = 'platinum';
              $requestJson['badge_count'] = 5;
              break;
            case 's1f45t':
                $requestJson['badge_type'] = 'gold';
                $requestJson['badge_count'] = 10;
              break;
            case '0' or 0:
                $requestJson['badge_type'] = 'silver';
                $requestJson['badge_count'] = 10;
              break;
            default:
                $requestJson['badge_type'] = 'silver';
                $requestJson['badge_count'] = 10;
          }
        $response = $this->service->create($requestJson);
        return $this->respond($response->getResponse(), $response->getCode());

    }

    public function show($id = null)
    {
        $response = $this->service->getById($id);
        return $this->respond($response->getResponse(), $response->getCode());

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

    public function getBadgesOfUser()
    {
        helper('jwt');
        $userId = toPayloadFromRequset($this->request)['id'];
        $response = $this->service->getBadgesOfUser($userId);
        if (!$response->isSuccess()){
            return $this->respond($response->getResponse(), $response->getCode());
        }
        return $this->respond($response->getData());
    }





    

}
