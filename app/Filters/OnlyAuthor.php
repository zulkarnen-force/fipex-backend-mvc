<?php

namespace App\Filters;

use App\Api\Domains\BadgeInventory\Repository\SqlBadgeInventoryRepository;
use App\Api\Domains\ProductMember\Model\ProductMember;
use App\Api\Domains\User\Model\User;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class OnlyAuthor implements FilterInterface
{
    use ResponseTrait;  

    private function isAuthorOfProduct($userId, $productId)
    {
        $u = new User();
        $q = $u->select()->join('products p', 'p.author_id = users.id')->where('users.id', $userId)->where('p.id', $productId)->where('users.       is_author', 1);
        $r = $q->get()->getResult();
        if ($r) {
            return true;
        }
        return false;
    }

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
        
        $productId = $request->getJSON(true)['product_id'];
        $reqUserId = $request->getJSON(true)['user_id'];
         helper('jwt');
        $payload = toPayloadFromRequset($request);
        $userId = $payload['id'];
        $isAuthorOfThisProduct = $this->isAuthorOfProduct($userId, $productId);

        $productMember = new ProductMember();
        $queryPMember = $productMember->where('user_id', $reqUserId)->where('product_id', $productId);
        $userHasProduct = $queryPMember->get()->getResult();
        
        if (!$isAuthorOfThisProduct) {
            $response = service('response');
            $response->setStatusCode(401);
            $response->setJSON(["message" => 'can only author of this product to add member', 'to_developer' => 'dont forget has filled is author and assign author_id on products this auth user']);
            return $response;
        }

        if ($userHasProduct) {
            $response = service('response');
            $response->setStatusCode(400);
            $response->setJSON(["message" => 'the user already has a this requset product']);
            return $response;            
        }

    }

    public function after(RequestInterface $request,ResponseInterface $response, $arguments = null)
    {
    }

}
