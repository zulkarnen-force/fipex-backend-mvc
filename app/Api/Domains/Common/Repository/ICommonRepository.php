<?php

namespace App\Api\Domains\Common\Repository;

interface ICommonRepository 
{
    function store($data);
    function list(array $fields = ['*']);
    function update(string $id, $data);
    function delete(string $id);
    function find(string $id, array $fields = ['*']);
}