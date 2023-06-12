<?php

namespace App\Filters;

use App\Models\BadgeInventory;
use App\Models\User;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;


class MakeSureEnoughBadge implements FilterInterface
{
    use ResponseTrait;  

    
    private function userHasValidate($userId)
    {
        $u = new User();
        $result = $u->select()->where('id', $userId)->first();
        return  $result['is_valid']; 
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $data = new \stdClass();
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
            $badgeInventory = new BadgeInventory();
            try {
                $userBadgeInventory = $badgeInventory->getByQuery(['user_id' => $userId], ['*']);
                $user = new User();
                $userHasVerification = $user->hasVerification($userId);
                if ($userHasVerification) {
                    throw new Exception('the user has not verified', 403);
                }
                
                // $exhibitionId = $result[0]->exhibition_id;
                // $data->exhibition_id = $exhibitionId;
                // return var_dump($exhibitionId);
                // $badgeType = $result[0]['badge_type'];
                // $data->user->badgeType = $badgeType;
                // return var_dump($exhibitionId, $badgeType);
                // return var_dump();
               
            } catch(Exception $e) {
                $response = service('response');
                $response->setStatusCode($e->getCode());
                $response->setJSON(['errors' => ["message" => $e->getMessage()]]); // , 'to_developer' => 'make sure has filled inventory badges of this user']
                return $response;
            }

            $badgeCount = (int) $userBadgeInventory[0]->badge_count;
            
            if ($badgeCount === 0) {
                $response = service('response');
                $response->setStatusCode(400);
                $response->setJSON(["message" => 'your badge is not enough 😥']);
                return $response;
            }
            return $request;
           
    }

    public function after(RequestInterface $request,ResponseInterface $response, $arguments = null)
    {
    }

}
