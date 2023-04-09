<?php

namespace App\Api\Domains\BadgeCollection\Repository;

use App\Api\Domains\BadgeInventory\Model\BadgeInventory;
use App\Api\Domains\BadgeInventory\Repository\SqlBadgeInventoryRepository;
use App\Api\Domains\Common\Repository\SqlCommonRepository;
use Exception;

class SqlBadgeCollectionRepository extends SqlCommonRepository implements IBadgeCollectionRepository
{
	public $model; 
	public function __construct()
    {
		parent::__construct('App\Api\Domains\BadgeCollection\Model\BadgeCollection');
		$this->model = model('App\Api\Domains\BadgeCollection\Model\BadgeCollection');
	}
	
	/**
	 * @param mixed $productId
	 * @return mixed
	 */
	public function getBadges($productId) 
	{
		try {
			$query = $this->model
            ->select("badge_type as type, count(*) as badges_total")
            ->where('badges_collection.product_id', $productId)->groupBy("badge_type");

			return $query->get()->getResult();
		} catch (Exception $e) {
			throw $e;
		}


	}

	public function getComments($productId)
    {
        $query = $this->model
            ->select("badges_collection.*, usr.name, usr.image_url as avatar, usr.bio, usr.id as user_id")
            ->join('users usr', 'usr.id = badges_collection.user_id')
            ->where('badges_collection.product_id', $productId)->orderBy('created_at', 'DESC');
        $result = $query->get()->getResult();
        return $result;
        
    }


	public function isEnoughBadges($userId)
	{
		$inventoryRepo = new SqlBadgeInventoryRepository();
		try {
			$badgeCount = $inventoryRepo->getWhere(['user_id' => $userId], ['badge_count']);
			return $badgeCount;
		} catch (Exception $e) {
			return $e;
		}
	}


	public function decrementBadgeUser($userId)
	{
		
		try {
			$inventory = new BadgeInventory();
			$query = $inventory->where('user_id', $userId)->where('badge_count >',  '0')->decrement('badge_count', 1);
			if ($query === false) {
				throw new Exception('error from decrement user badge');
			}
			return $query;
		} catch(Exception $e) {
			throw $e;
		}
		
	}


	
	/**
	 * @param mixed $userId
	 * @return mixed
	 */
	public function incrementBadgeUser($userId) {
		{
		
			try {
				$inventory = new BadgeInventory();
				$query = $inventory->where('user_id', $userId)->where('badge_count <',  '10')->increment('badge_count', 1);
				if ($query === false) {
					throw new Exception('error from increment user badge');
				}
				return $query;
			} catch(Exception $e) {
				throw $e;
			}
			
		}
	}


	public function isUserHasGivenBadge($userId, $productId)
	{
		try {
			$query = $this->model->where(['user_id' => $userId])->where(['product_id' => $productId]);
			$result = $query->get()->getResult();
			if ($result) { // jika sudah pernah ngasih badge
				return true;
			}
			return false;
		} catch (Exception $e) {
			throw $e;
		}
	}

	public function check($userId, $productId) 
	{
		try {
			$query = $this->model->join('exhibitons e', 'e.id = badges_collection');
			$result = $query->get()->getResult();
			if ($result) { // jika sudah pernah ngasih badge
				return true;
			}
			return false;
		} catch (Exception $e) {
			throw $e;
		}
	}

	
	/**
	 * @param mixed $userId
	 * @param mixed $productId
	 * @return mixed
	 */
	public function backBadgeFromProductToUser($userId, $productId)
	{
		try {
			$isHasGivenBadge = $this->isUserHasGivenBadge($userId, $productId);
			if (!$isHasGivenBadge) 
			{
				throw new Exception('the user has never assigned a badge to this product', 400);
			}
			$db = \Config\Database::connect();
			$db->transStart();
			$db->query('DELETE FROM badges_collection WHERE user_id = ? AND product_id = ?', [$userId, $productId]);
			$db->query('UPDATE badge_inventories SET badge_count = badge_count + 1 WHERE user_id = ? and badge_count < 10;', $userId);
			$db->transComplete();
		} catch (\Throwable $th) {
			throw $th;
		}

	}
	/**
	 * @param mixed $productId
	 * @return mixed
	 */
	public function getBadgesWithPoints($productId)
	{
		try {
			$sql = "
            	sum(case when badge_type = 'silver' then 1 else 0 end) as silver,
            	sum(case when badge_type = 'gold' then 1 else 0 end) as gold,
            	sum(case when badge_type = 'platinum' then 1 else 0 end) as platinum,
            	sum(case when badge_type = 'silver' then 25 else 0 end) as silver_points,
            	sum(case when badge_type = 'gold' then 50 else 0 end) as gold_points,
            	sum(case when badge_type = 'platinum' then 100 else 0 end) as platinum_points,
            	";

        	$query = $this->model->select($sql, false)->where('product_id', $productId);
        	$badges = $query->get()->getResult();
			


			// var_dump($result);
		return $badges;

		} catch (\Throwable $th) {
			throw $th;
		}


	}
}