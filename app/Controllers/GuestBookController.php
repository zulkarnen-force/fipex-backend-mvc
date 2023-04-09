<?php

namespace App\Controllers;

use App\Models\GuestBook;
use App\Services\GuestBookService;
use CodeIgniter\RESTful\ResourceController;

class GuestBookController extends ResourceController
{
    public $service;
    public function __construct() {
        $this->service = new GuestBookService(new GuestBook());
    }

    public function index()
    {

        $gBook = new GuestBook();
        $q = $gBook->orderBy('created_at', 'DESC')->
            select("guest_books.id id, guest_books.comment comment, u.id u_id, u.name u_name, u.email u_email, u.image_url u_img_url, u.bio u_bio, guest_books.created_at, guest_books.updated_at")->
            join('users u', 'u.id = guest_books.user_id');
        $r = $q->get()->getResult();
        
        array_map(function ($data) {
            $data->created_by = ['id' => $data->u_id, 'name' => $data->u_name, "email" => $data->u_email, "bio" => $data->u_bio, 'image_url' => $data->u_img_url];
            $data->timestamps = ['created_at' => $data->created_at, "updated_at" => $data->updated_at];
            unset($data->u_id, $data->u_name, $data->u_email, $data->u_bio, $data->u_img_url);
            return $data;
        }, $r);

        return $this->respond($r, 200);
    }

    public function create()
    {
        $requestJson = $this->request->getJson(true);
        $requestJson['exhibition_id'] = "63b0392f35476";
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

    public function showLimit($limit = null)
    {
        $gBook = new GuestBook();
        $q = $gBook->orderBy('created_at', 'DESC')->limit($limit)->offset(0)->
            select("guest_books.id id, guest_books.comment comment, u.id u_id, u.name u_name, u.email u_email, u.image_url u_img_url, u.bio u_bio, guest_books.created_at, guest_books.updated_at")->
            join('users u', 'u.id = guest_books.user_id');
        $r = $q->get()->getResult();
        
        array_map(function ($data) {
            $data->created_by = ['id' => $data->u_id, 'name' => $data->u_name, "email" => $data->u_email, "bio" => $data->u_bio, 'image_url' => $data->u_img_url];
            $data->timestamps = ['created_at' => $data->created_at, "updated_at" => $data->updated_at];
            unset($data->u_id, $data->u_name, $data->u_email, $data->u_bio, $data->u_img_url);
            return $data;
        }, $r);

        return $this->respond($r, 200);
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


}