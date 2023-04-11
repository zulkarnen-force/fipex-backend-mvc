<?php

namespace App\Models;

use App\Exceptions\ValidationException;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Model;
use Exception;
use Throwable;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id',
        'name',
        'email',
        'is_author',
        'password',
        'image_url',
        'bio'
    ];

    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'id'     => 'is_unique[users.id]',
        'name' => 'required',
        'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]|max_length[255]',
    ];
    protected $validationMessages = [

    ];


    
    protected $beforeInsert = ['encryptPassword'];
    protected $beforeUpdate = ['beforeUpdate'];

    protected function generateId($data)
    {
        if (isset($data['data']['id'])) {
            return $data;
        }
        
        $data['data']['id'] = uniqid();
        return $data;
    }
    protected function encryptPassword(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    protected function beforeUpdate(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    private function getUpdatedDataWithHashedPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $plaintextPassword = $data['data']['password'];
            $data['data']['password'] = $this->hashPassword($plaintextPassword);
        }
  
        return $data;
    }

    private function hashPassword(string $plaintextPassword): string
    {
        return password_hash($plaintextPassword, PASSWORD_BCRYPT);
    }
                                      
    public function findUserByEmailAddress(string $emailAddress)
    {
        $user = $this
            ->asArray()
            ->where(['email' => $emailAddress])
            ->first();

        if (!$user) 
            throw new Exception('User does not exist for specified email address');

        return $user;
    }
    public function findUserByUid(string $uid)
    {
        $user = $this
            ->asArray()
            ->where(['uid' => $uid])
            ->first();

        if (!$user) 
            throw new Exception('User does not exist for specified email address');

        return $user;
    }

    public function store($data)
    {
		try {
			$id = uniqid();
			$data['id'] = $id;
			$query = $this->insert($data);
			if ($query === false) {
				throw new ValidationException($this->errors(), "validation error", 400);
			}
			return $id;
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
			return $this->select($fields)->get()->getResult();
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
	public function updateById($id, $data = [])
    {
		
		$isResult = $this->update($id, $data);
		if ($isResult === false) 
		{
			throw new ValidationException($this->errors(), 'error on update data', 500);
		}
		return $isResult;
	}
	
	/**
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function deleteById(string $id)
    {
		try {
			$result = $this
				->where('id', $id)
				->delete();
			if ($result === false) {
				$errors = $this->errors();
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
	public function findById($id, $fields = ['*'])
    {
        try {
			$result = $this->select($fields)->find($id);
			if (!$result) {
				throw new Exception('record not found');
			}
			return $result;
        } catch (Exception $e) {
            throw $e;
        }
	}

	
	public function getByQuery($where = [],$fields = ["*"])
	{
		try {
			$query = $this->select($fields)->getWhere($where);
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
    
}
