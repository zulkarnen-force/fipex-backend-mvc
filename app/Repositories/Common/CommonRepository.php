<?php

namespace App\Repositories\Common;

use App\Utils\ErrorResponse;
use CodeIgniter\Model;
use Exception;
use Psr\Container\NotFoundExceptionInterface;

class CommonRepository implements RepositoryInterface 
{

    public  $model;
    public  $db;
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->db = \Config\Database::connect();
    }

    public function insert($user)
    {
        $result = $this->model->insert($user, true);
        
        if ($result === false) {
            return [false, $this->model->errors()];
        }

        return [true, $result];
    }


    public function find($id)
    {
        return $this->model->find(['id' => $id])[0]; 
    }

	public function list() {
		return $this->model->orderBy('id', 'DESC')->findAll();
	}

	public function delete($id)
	{
		
        $result = $this->model->delete(['id' => $id]);

        return $result;
	}

	public function update($id, $data)
	{
        return $this->model->update($id, $data);
	}

    public function getById($id)
    {

        $result = $this->model->find($id);
        if (is_null($result)) {
                throw new Exception("product not found");
        }
        return $result;
        
    }
}