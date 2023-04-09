<?php

namespace App\Api\Domains\BadgeCollection\Repository;

interface IBadgeCollectionRepository 
{
    function store($data);
    function list(array $fields = ['*']);
    function update(string $id, array $data = []);
    function delete(string $id);
    function find(string $id, array $fields = ['*']);
    function getWhere(array $where = [], array $fields = ["*"]);
    function getBadges($productId);
    function getBadgesWithPoints($productId);
    function getComments($productId);
    function decrementBadgeUser($userId);
    function incrementBadgeUser($userId);
    function isEnoughBadges($userId);
    function isUserHasGivenBadge($userId, $productId);
    function backBadgeFromProductToUser($userId, $productId);
    function check($userId, $productId);

    
}