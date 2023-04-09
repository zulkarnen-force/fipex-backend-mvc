<?php

namespace App\Api\Domains\Banner\Controller;

use CodeIgniter\RESTful\ResourceController;

class BannerController extends ResourceController
{
    public function __construct()
    {
    }

    public function index()
    {
        $banners = array();
        foreach(glob('public/images/banners/*.*') as $filename){
            array_push($banners, 'https://apis.ruang-ekspresi.id/fipex/'.$filename);
        }
        return $this->respond(['banners' => $banners]);
    }

}