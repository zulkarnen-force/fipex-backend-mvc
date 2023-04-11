<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\ValidationException;
use App\Utils\Response;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;
use Throwable;

class UserService
{
    public $model;
    function __construct(User $model)
    {
        $this->model = $model;
    }

 

    public function find($id, $fields = ["*"])
    {
        try {
            $user = $this->model->findById($id, $fields);
            $user['is_author'] = (bool) $user['is_author'];
            $user['is_admin'] = (bool) $user['is_admin'];
            return new Response(200, null, true, $user);
        } catch (Exception $th) {
            return new Response(404, $th->getMessage(), false);
        }
    }

    public function cast($data) {
        $data->is_author = (bool) $data->is_author;
        return $data;
    }
    public function list()
    {
        try {
            $users = $this->model->list(["id", "email", "name", "bio", 'is_author', 'is_admin', "image_url", "created_at", "updated_at"]);
            array_map(function ($user) {
                $user->is_author = (bool) $user->is_author;
                $user->is_admin = (bool) $user->is_admin;
                return $user;
            }, $users);
            return new Response(200, 'all users data', true, $users);
        } catch (Throwable $th) {
            return new Response(200, $th->getMessage(), false, false);
        }
    }

    public function create($data)
    {
        try {
            $insertedData = $this->model->store($data);
            if ($insertedData === false) {
                return new Response(400, 'error insert data', false, null, null);
            }
            $data = ['id' => $insertedData];
            return new Response(201, 'user created successfully', true, $data, null);
        } catch (ValidationException $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, $e->getErrors());
        } catch (Exception $e){
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        }

    }


    public function update(string $id, $data = [])
    {
        try {
            $this->model->findById($id);
            $userUpdated = $this->model->updateById($id, $data);
            if ($userUpdated === false) {
                throw new Exception('error on update data');
            }
            return new Response(201, 'user updated successfully', true, $userUpdated);
        } catch (ValidationException $e) {
            return new Response(400, $e->getErrors(), false, null, null);
        } catch (Exception $e) {
            return new Response(400, $e->getMessage(), false, null, null);
        }
    }

    public function delete($id)
    {
        try {
            $response = $this->model->findById($id);
            $deleted = $this->model->deleteById($id);
            return new Response(200, 'exhibition deleted', true, $response, null);
        } catch (DatabaseException $th) {
            return new Response($th->getCode(), $th->getMessage(), false, null);
        } catch (Exception $th) {
            return new Response(404, $th->getMessage(), false, null);
        }
    }

    public function getById($id)
    {
        
        try {
            $result = $this->model->findById($id);
            return new Response(200, 'product data', true, $result);
        } catch (Exception $e) {
            return new Response(400, $e->getMessage(), false);
        }
    }


    public function login($request)
    {
        try {
            $user = $this->model->getByQuery(['email' => $request['email']])[0];
            $hashPassword = $user->password;
            $plainPassword = $request['password'];
            if (!password_verify($plainPassword, $hashPassword)) {
                return new Response(400, 'password not match', false);
            }
            
            helper('jwt');
            $token = getSignedJWTForUser($user->id, $user->email,  $user->is_author, $user->name);
            $response = new Response(200, 'user authenticated', true);
            $response->setResult(['token' => $token]);
            return $response;
        } catch (ValidationException $e) {
            return new Response(400, $e->getErrors(), false);
        } catch (Exception $e) {
            return new Response(400, $e->getMessage(), false);
        }
    }


    public function getProductByAuthorId(string $userId, array $fields = ["*"])
    {
        $result = $this->model->getWhere(['author_id' => $userId], $fields);
        if ($result === false) {
            return new Response(404, 'product not found', false, null, null);
        };
        return new Response(200, 'product of user', true, $result, null);
    }


    public function getBadgesOfUser($userId, $fields = ["*"])
    {
        try {
            $result = $this->model->getWhere(['user_id' => $userId], $fields);
            return new Response(200, 'badges of user', true, $result, null);
        } catch (Exception $e) {
            return new Response(404, $e->getMessage(), false, null, null);
        }
    }


    public function storeImageFromBase64($path, $base64)
    {
        try {
            $image = base64_decode($base64);
            $file = file_put_contents($path, $image);
            return $path;
        } catch (Throwable $th) {
            throw $th;
        }
    }

}

?>