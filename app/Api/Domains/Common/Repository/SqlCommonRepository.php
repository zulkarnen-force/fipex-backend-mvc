<?php

namespace App\Api\Domains\Common\Repository;

use App\Exceptions\ValidationException;
use App\Utils\Response;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\Response as HTTPResponse;
use CodeIgniter\Model;
use Exception;
use Throwable;

class SqlCommonRepository implements ICommonRepository 
{
	public $model;
    public function __construct(string $className)
    {
        $this->model = model($className);
	}
	
	/**
	 * @return mixed
	 */
	public function store($data)
    {
		try {
			$query = $this->model->insert($data);
			if ($query === false) {
				throw new ValidationException($this->model->errors(), "validation error", 400);
			}
			return true;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * @return mixed
	 */
	public function list(array $fields = ['*'])
    {
		try {
			return $this->model->select($fields)->get()->getResult();
		} catch (Throwable $th) {
			throw $th;
		}
	}
	
	/**
	 *
	 * @param string $id
	 * @param array $data
	 * @return bool|Exception
	 */
	public function update($id, $data = [])
    {
		
		$isResult = $this->model->update($id, $data);
		if ($isResult === false) 
		{
			throw new ValidationException($this->model->errors(), 'error on update data', 500);
		}
		return $isResult;
	}
	
	/**
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function delete(string $id)
    {
		try {
			$result = $this->model
				->where('id', $id)
				->delete();
			if ($result === false) {
				$errors = $this->model->errors();
				throw new DatabaseException($errors["CodeIgniter\Database\MySQLi\Connection"], HTTPResponse::HTTP_CONFLICT);
			} 
			return $result;
		} catch (Exception $e) {
			throw $e;
		}

	}
	
	/**
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function find($id, $fields = ['*'])
    {
        try {
			$result = $this->model->find($id);
			if (!$result) {
				throw new Exception('record not found');
			}
			return $result;
        } catch (Exception $e) {
            throw $e;
        }
	}

	
	public function getWhere($where = [],$fields = ["*"])
	{
		try {
			$query = $this->model->select($fields)->getWhere($where);
			if ($query === false) {
				throw new DatabaseException("errors");
			}
			
			$result = $query->getResult();
		
			if (empty($result)) {
				throw new Exception("record not found", 400);
			}

			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * @param mixed $model 
	 * @return self
	 */


	/**
	 * @param mixed $model 
	 * @return self
	 */
	public function setModel($model): self {
		$this->model = $model;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getModel() : Model {
		return $this->model;
	}
}