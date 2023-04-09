<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;
use App\Exceptions\ValidationException;
use Throwable;


class BadgeCollection extends Model
{
    protected $table = 'badges_collection';
    protected $primaryKey = 'id';
    protected $beforeInsert = ['generateId'];
    protected $allowedFields = [
        'id',
        'badge_type',
        'comment',
        'exhibition_id',
        'product_id',
        'user_id',
    ];

    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'badge_type'        => 'required|in_list[silver,gold,platinum]',
        'user_id'        => 'required',
        'exhibition_id'        => 'required'
    ];

    protected $validationMessages = [
        'comment' => [
            'product_id' => 'product_id required hehe',
        ],
        'user_id' => [
            'required' => 'user id required hehe',
        ],
    ];

    protected function generateId($data)
    {
        $data['data']['id'] = uniqid();
        return $data;
    }

        // CRUD
    
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
        
        /**
         * @return mixed
         */
        public function list(array $fields = ['*'])
        {
            try {
                return $this->select($fields)->get()->getResult();
            } catch (Throwable $th) {
                throw $th;
            }
        }
        
        /**
         *
         * @param string $id
         * @param array $data
         * @return bool|Exception
         */
        public function updateById($id, $data = [])
        {
            
            $isResult = $this->update($id, $data);
            if ($isResult === false) 
            {
                throw new ValidationException($this->errors(), 'error on update data', 500);
            }
            return $isResult;
        }
        
        /**
         *
         * @param string $id
         * @return mixed
         */
        public function deleteById(string $id)
        {
            try {
                $result = $this
                    ->where('id', $id)
                    ->delete();
                if ($result === false) {
                    $errors = $this->errors();
                    throw new DatabaseException($errors["CodeIgniter\Database\MySQLi\Connection"], HTTPResponse::HTTP_CONFLICT);
                } 
                return $result;
            } catch (Exception $e) {
                throw $e;
            }
    
        }
        
        /**
         *
         * @param string $id
         * @return mixed
         */
        public function findById($id, $fields = ['*'])
        {
            try {
                $result = $this->select($fields)->find($id);
                if (!$result) {
                    throw new Exception('record not found');
                }
                return $result;
            } catch (Exception $e) {
                throw $e;
            }
        }
    
        
        public function getByQuery($where = [],$fields = ["*"])
        {
            try {
                $query = $this->select($fields)->getWhere($where);
                if ($query === false) {
                    throw new DatabaseException("errors");
                }
                
                $result = $query->getResult();
            
                if (empty($result)) {
                    throw new Exception("record not found", 400);
                }
    
                return $result;
            } catch (Exception $e) {
                throw $e;
            }
    }


    
	/**
	 * @param mixed $productId
	 * @return mixed
	 */
	public function getBadges($productId) 
	{
		try {
			$query = $this
            ->select("badge_type as type, count(*) as badges_total")
            ->where('badges_collection.product_id', $productId)->groupBy("badge_type");

			return $query->get()->getResult();
		} catch (Exception $e) {
			throw $e;
		}


	}

	public function getComments($productId)
    {
        $query = $this
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
			$query = $this->where(['user_id' => $userId])->where(['product_id' => $productId]);
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
			$query = $this->join('exhibitons e', 'e.id = badges_collection');
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

        	$query = $this->select($sql, false)->where('product_id', $productId);
        	$badges = $query->get()->getResult();
			


			// var_dump($result);
		return $badges;

		} catch (\Throwable $th) {
			throw $th;
		}


	}

}
