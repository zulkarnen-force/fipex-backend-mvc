<?php

namespace App\Api\Domains\Product\Repository;

interface IProductRepository 
{
    function store($data);
    function list(array $fields = ['*']);
    function update(string $id, array $data = []);
    function delete(string $id);
    function find(string $id, array $fields = ['*']);
    function getWhere(array $where = [], array $fields = ["*"]);
    function getProductDetail(string $productId);
    function listProductsWithThumbs();
    function getBiggestPointOfProducts(string $categoryId);
    function getProductOfAuthor($authorId);
}