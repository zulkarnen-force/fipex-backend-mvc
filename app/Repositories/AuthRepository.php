<?php

namespace App\Repositories;

use App\Exceptions\ValidationException;
use App\Repositories\Common\CommonRepository;
use CodeIgniter\Model;
use Exception;

class AuthRepository extends CommonRepository 
{

    public function __construct(Model $model)
    {
        parent::__construct($model);
    }


    public function save($request)
    {
        try {
            $result = $this->model->insert($request);
            if ($result === false) {
                throw new ValidationException($this->model->errors());
            }
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }
	
    public function getByEmail(string $email)
    {
        $user = $this->model->where(['email' => $email])->first();

        if (!$user || empty($user))
        {
            throw new Exception('user not found');
        }

        return $user;
    }



}