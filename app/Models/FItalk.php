<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;
use App\Exceptions\ValidationException;
use CodeIgniter\Database\Exceptions\DatabaseException;


class Fitalk extends Model
{
    protected $table = 'FiTalksParticipants';
    protected $primaryKey = 'id';
    // {
    //     "id": "234324234",
    //     "name": "Test",
    //     "phone": "085853878030",
    //     "email": "test@webmail.uad.ac.id",
    //     "institution": "Universitas Ahmad Dahlan",
    //     "profession": "Mahasiswa",
    //     "alreadyPresent": "0"
    // }
    protected $allowedFields = [
        "name",
        "phone",
        "email",
        "institution",
        "profession",
        "alreadyPresent",
    ];

    protected $updatedField = 'updated_at';
    protected $validationRules = [

    ];
    protected $validationMessages = [

    ];

    

    protected function generateId($data)
    {
        if (isset($data['data']['id'])) {
            return $data;
        }
        
        $data['data']['id'] = uniqid();
        return $data;
    }
    
    protected $beforeInsert = ['generateId'];


    public function store($data)
    {
		try {
			$query = $this->insert($data);
			if ($query === false) {
				throw new ValidationException($this->errors(), "validation error", 400);
			}
			return true;
		} catch (Exception $e) {
			throw $e;
		}
	}
    public function list()
    {
        return $this->orderBy('alreadyPresent', 'desc')->get()->getResult();
    }

    // public function setStatus($id, $data)
    // {
    //     try {
    //         $q = $this->update($id, ['alreadyPresent' => true]);
    //         return $q;
    //     } catch (\Throwable $th) {
    //         throw $th;
    //     }
    // }

    public function setPresentTrue($id)
    {
        try {
            $q = $this->update($id, ['alreadyPresent' => true]);
            return var_dump($q);
            return $q;
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function getByQuery($where = [],$fields = ["*"])
	{
		try {
			$query = $this->select($fields)->getWhere($where);
            $result = $query->getResult();

			if ($query === false) {
				throw new DatabaseException("errors");
			}

			if (empty($result)) {
				throw new Exception("record not found", 400);
			}

			return $result;
		} catch (Exception $e) {
			throw $e;
		}
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
    
}
