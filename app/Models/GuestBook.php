<?php

namespace App\Models;
use CodeIgniter\Database\Exceptions\DatabaseException;
use App\Exceptions\ValidationException;
use Throwable;
use CodeIgniter\Model;
use Exception;


class GuestBook extends Model
{
    protected $table = 'guest_books';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'comment',
        'exhibition_id',
        'user_id',
        'image_url',
    ];

    protected $updatedField = 'updated_at';

    
    // protected $beforeInsert   = ['generateId'];
    protected function generateId($data)
    {
        $data['data']['id'] = uniqid();
        return $data;
    }
    protected $validationRules = [
        'comment'        => 'required',
        'user_id'        => 'required',
    ];

    protected $validationMessages = [
        'comment' => [
            'required' => 'comment required hehe',
        ],
        'user_id' => [
            'required' => 'user id required hehe',
        ],
    ];
    
    // CRUD
    
    public function store($data)
    {
		try {
			$data['id'] = uniqid();
			$query = $this->insert($data);
			if ($query === false) {
				throw new ValidationException($this->errors(), "validation error", 400);
			}
			return $data['id'];
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public function list($fields = ['*'])
	{
		$q = $this->orderBy('created_at', 'DESC');
		$r = $q->get()->getResult();
		return $r;
	}
	// /**
	//  * @return mixed
	//  */
	// public function list(array $fields = ['*'])
    // {
	// 	try {
	// 		return $this->select($fields)->get()->getResult();
	// 	} catch (Throwable $th) {
	// 		throw $th;
	// 	}
	// }
	
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
