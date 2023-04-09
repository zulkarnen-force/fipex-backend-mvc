<?php

namespace App\Filters;

use App\Models\BadgeInventory;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class MakeSureEnoughBadge implements FilterInterface
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
            $userId = toPayloadFromRequset($request)['id'];
            $repo = new BadgeInventory();
            try {
                $result = $repo->getByQuery(['user_id' => $userId], ['*']);
                return var_dump($result);
            } catch(Exception $e) {
                $response = service('response');
                $response->setStatusCode($e->getCode());
                $response->setJSON(["message" => $e->getMessage(), 'to_developer' => 'make sure has filled inventory badges of this user']);
                return $response;
            }

            $badgeCount = (int) $result[0]->badge_count;

            if ($badgeCount === 0) {
                $response = service('response');
                $response->setStatusCode(400);
                $response->setJSON(["message" => 'your badge is not enough 😥']);
                return $response;
            }
           
    }

    public function after(RequestInterface $request,ResponseInterface $response, $arguments = null)
    {
    }

}
