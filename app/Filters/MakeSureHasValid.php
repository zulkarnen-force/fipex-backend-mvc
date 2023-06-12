<?php

namespace App\Filters;

use App\Models\ProductMember;
use App\Models\User;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class MakeSureHasValid implements FilterInterface
{
    use ResponseTrait;  

    private function getUser($email)
    {
        $u = new User();
        $q = $u->select()->where('email', $email)->first();
        if (!$q) {
            throw new Exception('user not found', 400);
        }
        return $q;
    }

    public function before(RequestInterface $request, $arguments = null)
    { 
        try {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'email' => 'required',
                'password' => 'required',
            ]);
            $validation->withRequest($request)->run();
            $response = service('response');
            if (!empty($validation->getErrors())) {     
                $response->setJSON(['errors' => $validation->getErrors()]);
                $response->setStatusCode(400);
                return $response;
            }
            $email = $request->getJSON(true)['email'];
            $user = $this->getUser($email);
            if (!$user) {
                throw new Exception('user not found', 404);
            }
            $isValid = $user['is_valid'];
            if (!$isValid) {
                throw new Exception('the account has not been verified', 403);
            }
        } catch (\Exception $e) {
            $response = service('response');
            $response->setStatusCode($e->getCode());
            $response->setJSON(["message" => $e->getMessage()]);
            return $response;
        } catch (\Throwable $e) {
            $response = service('response');
            $response->setStatusCode($e->getCode());
            $response->setJSON(["message" => $e->getMessage()]);
            return $response;
        }

    }

    public function after(RequestInterface $request,ResponseInterface $response, $arguments = null)
    {
    }

}
