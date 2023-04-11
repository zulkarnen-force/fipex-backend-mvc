<?php

namespace App\Services;


use App\Exceptions\ValidationException;
use App\Models\Fitalk;
use App\Utils\Response;
use Exception;
use Throwable;

class FitalkService
{
    public $model;
    function __construct(Fitalk $model)
    {
        $this->model = $model;
    }

    public function create($data)
    {
        try {
            $insertedData = $this->model->store($data);
            if ($insertedData === false) {
                return new Response(400, 'error insert data', false, null, null);
            }
            return new Response(201, 'fitalk partisipant created successfully', true, null, null);
        } catch (ValidationException $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, $e->getErrors());
        } catch (Exception $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        } catch (Throwable $e){
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        }

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
        $fitalkPartisipants = $this->model->list($fields);
        return new Response(200, 'fitalk partisipants', true, $fitalkPartisipants);
    }

    public function setPresent($id) {
       
        try {
            $res = $this->model->setPresentTrue($id);
            return new Response(200, null, true, $res);
        } catch (\Throwable $th) {
            return new Response(400, 'set present failure', false, $res);
        }
    }


    public function getByQuery($query) {
        try {
            $res = $this->model->getByQuery($query);
            return new Response(200, null, true, $res);
        } catch (Exception $e) {
            return new Response(404, $e->getMessage(), false, null, null);
        }
    }

    

}

?>