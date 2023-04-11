<?php

namespace App\Controllers;

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
            array_push($banners, base_url().'/'.$filename); //https://apis.ruang-ekspresi.id/fipex/
        }
        return $this->respond(['banners' => $banners]);
    }

}