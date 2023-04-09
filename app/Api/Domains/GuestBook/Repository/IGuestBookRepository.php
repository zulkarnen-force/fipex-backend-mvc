<?php

namespace App\Api\Domains\GuestBook\Repository;

interface IGuestBookRepository 
{
    function store($data);
    function list(array $fields = ['*']);
    function update(string $id, array $data = []);
    function delete(string $id);
    function find(string $id, array $fields = ['*']);
    function getWhere(array $where = [], array $fields = ["*"]);

}