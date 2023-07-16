<?php

namespace App\Services;


use App\Exceptions\ValidationException;
use App\Models\BadgeInventory;
use App\Models\GuestBook;
use App\Models\ProductThumbnail;
use App\Utils\Response;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;

class BadgeInventoryService
{
    public $model;
    function __construct(BadgeInventory $model)
    {
        $this->model = $model;
    }


    public function find($id)
    {
        try {
            $user = $this->model->findById($id, ['name']);
            return new Response(200, null, true, $user);
        } catch (Exception $th) {
            return new Response(404, $th->getMessage(), false);
        }
    }


    public function list()
    {
        $users = $this->model->list();
        return new Response(200, 'all badge inventories data', true, $users);
    }


    public function create($data)
    {
        $guestBook = new GuestBook();
        try {
            $guestBook->store($data);
            $insertedData = $this->model->store($data);
            if ($insertedData === false) {
                return new Response(400, 'error insert data', false, null, null);
            }
            $data = ['id' => $insertedData];
            return new Response(201, 'badge inventory created successfully', true, $data, null);
        } catch (ValidationException $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, $e->getErrors());
        } catch (Exception $e){
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        }

    }


    public function update(string $id, $data = [])
    {
        try {
            $this->model->find($id);
            $userUpdated = $this->model->updateById($id, $data);
            if ($userUpdated === false) {
                throw new Exception('error on update data');
            }
            return new Response(201, 'badge inventory updated successfully', true, $userUpdated);
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
            return new Response(200, 'badge inventory has been deleted successfully', true, $response, null);
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


    public function getProductByAuthorId(string $userId, array $fields = ["*"])
    {
        $result = $this->model->getByQuery(['author_id' => $userId], $fields);
        if ($result === false) {
            return new Response(404, 'product not found', false, null, null);
        };
        return new Response(200, 'product of user', true, $result, null);
    }


    public function getBadgesOfUser($userId, $fields = ["*"])
    {
        try {
            $result = $this->model->getByQuery(['user_id' => $userId], $fields);
            if (!$result) {
                throw new Exception('badge of user not found');
            }
            return new Response(200, 'badges of user', true, $result[0], null);
        } catch (Exception $e) {
            return new Response(404, $e->getMessage(), false, null, null);
        }

    }

    

 


}

?>