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
        'bio',
		'otp',
		'otp_expires',
		'is_valid',
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

	
	public function getByQuery($query = [], $fields = ["*"])
	{
		try {
			$q = $this->select($fields)->getWhere($query);
			if ($q === false) {
				throw new DatabaseException("errors");
			}
			
			$result = $q->getResult();
		
			if (empty($result)) {
				throw new Exception("record not found", 404);
			}

			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}


	public function findOneByEmail($email = "")
	{
		try {
			$user = $this->where(['email' => $email])->first();
			if (!$user) {
				throw new Exception('user not found', 404);
			}
			return $user;
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	public function verifyOtp($otp)
	{
		$r = $this->where('otp', $otp)->first();
		if (!$r) {
			throw new Exception('Your otp is invalid', 400);
		}
		$activation_expired = time() > $r['otp_expires'];
		if ($activation_expired) {
			throw new Exception('OTP code has expired', 400);
		}
		return true;
	}

	public function setActiveAccount($otp)
	{
		$this->set('is_valid', true);
		$this->where('otp', $otp);
		$this->update();
	}


	public function setOtp($email, $otp, $otp_expires)
	{
		try {
			$user = $this->findOneByEmail($email);
			$this->set('otp', $otp);
			$this->set('otp_expires', $otp_expires);
			$this->where('email', $email);
			$this->update();
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	public function hasVerification($userId)
    {
        $result = $this->select()->where('id', $userId)->first();
        return  $result['is_valid']; 
    }

	public function isValidUser($otp)
    {
        $u = new User();
        $validated = $u->select()->where('otp', $otp)->where('is_valid', true)->first();
        if ($validated) {
            throw new Exception('user has been verification', 400);
        }
        return true;
    }
    
}
