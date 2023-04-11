<?php

namespace App\Controllers;

use App\Models\Fitalk;
use App\Services\FitalkService;
use CodeIgniter\RESTful\ResourceController;
use Exception;

class FitalkController extends ResourceController
{
    public $service;
    public function __construct() {
        $this->service = new FitalkService(new Fitalk());
    }


    public function create()
    {
        $requestJson = $this->request->getJson(true);
        $response = $this->service->create($requestJson);
        return $this->respond($response->getResponse(), $response->getCode());

    }
    public function list()
    {
        // $m = new Fitalk();
        // try {
        //     return $this->respond($m->list(), 200);
        // } catch (\Throwable $th) {
        //     return $this->respond($th->getMessage(), 400);

        // }
        try {
            //code...
            $response = $this->service->list();
            return $this->respond($response->getResponse(), $response->getCode());
        } catch (\Throwable $th) {
            return $this->respond($response->getResponse(), $response->getCode());
        }
    }


    public function setPresent($id)
    {
        // $m = new Fitalk();
        // $rq = $this->request->getJSON(true);
        // try {
        //     $res = $m->setStatus($id, $rq);
        //     return $this->respond($res, 200);
        // } catch (\Throwable $th) {
        //     return $this->respond($th->getMessage(), 400);
        // }
        try {
            //code...
            $this->service->setPresent($id);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    
    
    public function isExists($email)
    {
        // $m = new Fitalk();
        // // $email = $this->request->getJSON(true)['email'];
        // try {
        //     $r = $m->select('id')->getWhere(['email' => $email])->getResult();
        //     if (!$r) {
        //         return $this->respond(['message' => 'partisipant not found'], 404);
        //     }
        //     return $this->respond($r[0], 200);
        // } catch (\Throwable $th) {
        //     return $this->respond($th->getMessage(), 400);
        // }
        $response = $this->service->getByQuery(['email' => $email]);
        return $this->respond($response->getResponse(), $response->getCode());
        // return $this->respond($response->getResponse(), $response->getCode());
        // try {
        //     //code...
            

        // } catch (\Throwable $th) {
        //     //throw $th;
        //     var_dump($response);
        //     return $this->respond($response->getResponse(), $response->getCode());
        // }
    }



}