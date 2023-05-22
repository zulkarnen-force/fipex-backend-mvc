<?php

use App\Models\UserModel;
use CodeIgniter\HTTP\Request;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getJWTFromRequest($authenticationHeader): string
{
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid JWT in request');
    }
    //JWT is sent from client in the format Bearer XXXXXXXXX
    return explode(' ', $authenticationHeader)[1];
}

function validateJWTFromRequest(string $encodedToken)
{
    $key = Services::getSecretKey();    
    $decodedToken = JWT::decode($encodedToken, new Key($key, 'HS256'));   
    return (array)$decodedToken;
}


function toToken(string $authorization)
{
    return explode(" ", $authorization)[1];
}
function toPayloadFromRequset(Request $request)
{
    $authorizationHeader = $request->getServer('HTTP_AUTHORIZATION');
    $token = toToken($authorizationHeader);
    return validateJWTFromRequest($token);
}

function getSignedJWTForUser($user)
{   
    $issuedAtTime = time();
    $tokenTimeToLive = strtotime('+1 day', $issuedAtTime);
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'email' => $user->email,
        'id' => $user->id,
        'is_author' =>  (bool) $user->is_author,
        'is_admin' =>  (bool) $user->is_admin,
        'name' =>  $user->name,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration,
    ]; 
    $jwt = JWT::encode($payload, Services::getSecretKey(), 'HS256');
    return $jwt;
}





function toPayload(string $authorization)
{
    $token = explode(" ", $authorization)[1];
    return validateJWTFromRequest($token);
}

