<?php

namespace App\Services;


use App\Api\Domains\ProductMember\model\IProductMembermodel;
use App\Api\Domains\Product\Model\Product;
use App\Exceptions\ValidationException;
use App\Models\ProductMember;
use App\Utils\Response;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;
use Throwable;

class ProductMemberService
{
    public $model;
    function __construct(ProductMember $model)
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
        $productMembers = $this->model->list($fields);
        return new Response(200, 'all product members data', true, $productMembers);
    }


    public function create($data)
    {
        try {
            $insertedData = $this->model->store($data);
            if ($insertedData === false) {
                return new Response(400, 'error insert data', false, null, null);
            }
            return new Response(201, 'product member inserted successfully', true, null, null);
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
            return new Response(200, 'product member updated successfully', true, $userUpdated);
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
            return new Response(200,'product member deleted succesfully', true, $response, null);
        } catch (DatabaseException $th) {
            return new Response(500, $th->getMessage(), false, null);
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


    public function getProductOfMemberUser($id)
    {
        try {
            $result = $this->model->getProductOfMemberUser($id);
            return new Response(200, 'product of user', true, $result);
        } catch (Exception $e) {
            return new Response(404, $e->getMessage(), false, null, null);
        } catch (Throwable $th) {
            return new Response(404, $th->getMessage(), false, null, null);
        }
    }



}

?>