<?php

namespace App\Filters;

use App\Models\BadgeInventory;
use App\Models\User;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

/**
 * To make sure one user has one inventory at one exhibition
 */
class EnsureOneUserOneBadgeInventory implements FilterInterface
{
    use ResponseTrait;  

    public function before(RequestInterface $request, $arguments = null)
    {
        $authenticationHeader = $request->getServer('HTTP_AUTHORIZATION');
        if( is_null($authenticationHeader) || empty($authenticationHeader)) {
            $response = service('response');
            $response->setBody('access denied');
            $response->setStatusCode(401);
            $response->setJSON(["code" => 401, 'errors' =>  [
                'message' => 'place your token on header please ✌'
            ]]);
            return $response;
        } 
            helper('jwt');
            $payload = toPayloadFromRequset($request);
            $requestBody = $request->getJSON(true);
            $exhibitionId = $requestBody['exhibition_id'];
            $userId = $payload['id'];
            $inventoryModel = new BadgeInventory();
            $query = $inventoryModel->where('user_id', $userId)->where('exhibition_id', $exhibitionId);
            $userHasInventory = $query->get()->getResult();
            if ($userHasInventory) {
                 $response = service('response');
                $response->setStatusCode(400);
                $response->setJSON(["message" => 'this user has inventory for this exhibition']);
                return $response;
            }
                      
    }

    public function after(RequestInterface $request,ResponseInterface $response, $arguments = null)
    {
    }

}
