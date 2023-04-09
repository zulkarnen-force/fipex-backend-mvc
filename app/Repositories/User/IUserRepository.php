<?php

namespace App\Repositories\User;

interface IUserRepository 
{
    function store($data);
    function list();
    function update(string $id, $data);
    function delete(string $id);
    function find(string $id);

}