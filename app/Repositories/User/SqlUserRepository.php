<?php

namespace App\Repositories\User;

use App\Exceptions\ValidationException;
use Exception;

class SqlUserRepository implements IUserRepository 
{
	private $model;
    public function __construct()
    {
        $this->model = model('User');
	}

    
	/**
	 * @return mixed
	 */
	public function store($data)
    {
		try {
			$query = $this->model->insert($data, true);
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
	public function list()
    {
        $query = $this->model->get();
        return $query->getResult();
	}
	
	/**
	 *
	 * @param string $id
	 * @param array $data
	 * @return bool|Exception
	 */
	public function update($id, $data): bool|Exception
    {
		try {
			$isResult = $this->model->update($id, $data);
			if ($isResult === false) 
			{
				throw new Exception("error updated data");
			}
		return $isResult;
		} catch (Exception $e) {
			throw $e;
		}
	
		
	}
	
	/**
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function delete(string $id)
    {
		try {
			return $this->model
				->where('id', $id)
				->delete();
		} catch (Exception $e) {
			throw $e;
		}

	}
	
	/**
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function find(string $id)
    {
        try {
            $query = $this->model->select('id, name, email, image_url, bio, created_at, updated_at')->where('id', $id);
			$result = $query->get()->getResult();

			if (empty($result)) {
				throw new Exception('record not found');
			}

            return $result[0];
        } catch (Exception $e) {
            throw $e;
        }

	}
}