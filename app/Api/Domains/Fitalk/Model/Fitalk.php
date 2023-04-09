<?php

namespace App\Api\Domains\Fitalk\Model;

use CodeIgniter\Model;
use Exception;

class Fitalk extends Model
{
    protected $table = 'FiTalksParticipants';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'alreadyPresent'
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

    public function list()
    {
        return $this->orderBy('alreadyPresent', 'desc')->get()->getResult();
    }

    public function setStatus($id, $data)
    {
        try {
            $q = $this->update($id, $data);
            return $q;
        } catch (\Throwable $th) {
            throw $th;
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
