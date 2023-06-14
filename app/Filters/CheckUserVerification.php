<?php

namespace App\Filters;

use App\Models\User;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class CheckUserVerification implements FilterInterface
{
    use ResponseTrait;  


    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            $userModel = new User();
            if ($request->getGet('otp')) {
                $otp = $request->getGet('otp');
            } else {
                $otp = $request->getJSON(true)['otp'];
            }
            $validationStatus = $userModel->isValidUser($otp);
        } catch (\Throwable $e) {
            if ($request->getMethod() === 'get') {
                return redirect()->to('pages/verify/failure?message='.$e->getMessage());
            }
            $response = service('response');
            $response->setStatusCode($e->getCode());
            $response->setJSON(["success" => false, "message" => $e->getMessage()]);
            return $response;
        }
      
            
            // $query = $inventoryModel->where('user_id', $userId)->where('exhibition_id', $exhibitionId);
            // $userHasInventory = $query->get()->getResult();
            // if ($userHasInventory) {
            //      $response = service('response');
            //     $response->setStatusCode(400);
            //     $response->setJSON(["message" => 'this user has inventory for this exhibition']);
            //     return $response;
            // }
                      
    }

    public function after(RequestInterface $request,ResponseInterface $response, $arguments = null)
    {
    }

}
