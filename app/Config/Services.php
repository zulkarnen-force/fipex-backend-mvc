<?php

namespace Config;

use App\Models\UserModel;
use CodeIgniter\Config\BaseService;
use Exception;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */
    public static function getSecretKey(){
        return 'kzUf4sxss4AeG5uHkNZAqT1Nyi1zVfpz'; //getenv('JWT_SECRET_KEY');
    } 


    public function getUserData($authenticationHeader){
        // $authenticationHeader = $requset->getServer('HTTP_AUTHORIZATION');
        try{
            helper('jwt');
            $encodedToken = getJWTFromRequest($authenticationHeader);
            $userToken = validateJWTFromRequest($encodedToken);
            $userModel = new UserModel();
            $user = $userModel->findUserByEmailAddress($userToken->email);
            unset($user['password']);
            unset($user['created_at']);
            unset($user['updated_at']);
            return $user;
        }catch (Exception $e) {
            return $e;
        }
    }



    // public function getPayload()
    // {
    //     helper('jwt');
    //     return 
    // }
}


