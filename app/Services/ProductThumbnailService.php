<?php

namespace App\Services;


use App\Exceptions\ValidationException;
use App\Models\ProductThumbnail;
use App\Utils\Response;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;
use Throwable;

class ProductThumbnailService
{
    public $model;
    function __construct(ProductThumbnail $model)
    {
        $this->model = $model;
    }


    public function find($id, $fields = ["*"])
    {
        try {
            $user = $this->model->findById($id, $fields);
            return new Response(200, null, true, $user);
        } catch (Exception $th) {
            return new Response(404, $th->getMessage(), false);
        }
    }


    public function list($fields = ["*"])
    {
        $exhibitions = $this->model->list($fields);
        return new Response(200, 'all exhibitions data', true, $exhibitions);
    }


    public function create($data)
    {
        try {
            $insertedData = $this->model->store($data);
            if ($insertedData === false) {
                return new Response(400, 'error insert data', false, null, null);
            }
            $data = ['id' => $insertedData];
            return new Response(201, 'product thumbnail inserted successfully', true, $data, null);
        } catch (ValidationException $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, $e->getErrors());
        } catch (Exception $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        } catch (Throwable $e){
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
            return new Response(200, 'product thumbnail updated successfully', true, $userUpdated);
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
            return new Response(200, 'product thumbnail data', true, $result);
        } catch (Exception $e) {
            return new Response(400, $e->getMessage(), false);
        }
    }

    public function getProductByAuthorId(string $userId, array $fields = ["*"])
    {
        $result = $this->model->getByQuery(['author_id' => $userId], $fields);
        if ($result === false) {
            return new Response(404, 'product thumbnail not found', false, null, null);
        };
        return new Response(200, 'product thumbnail of user', true, $result, null);
    }


    public function getBadgesOfUser($userId, $fields = ["*"])
    {
        try {
            $result = $this->model->getByQuery(['user_id' => $userId], $fields);
            return new Response(200, 'badges of user', true, $result, null);
        } catch (Exception $e) {
            return new Response(404, $e->getMessage(), false, null, null);
        }

    }
    
        public function storeImageFromBase64($path, $base64)
    {
        try {
            $image = base64_decode($base64);
            file_put_contents($path, $image);
            return $path;
        } catch (Throwable $th) {
            throw $th;
        }
    }

    

 


}

?>