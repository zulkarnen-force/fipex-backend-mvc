<?php

namespace App\Models;
use CodeIgniter\Database\Exceptions\DatabaseException;
use App\Exceptions\ValidationException;
use Throwable;
use CodeIgniter\Model;
use Exception;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'name',
        'description',
        'exhibition_id',
        'category_id',
        'author_id',
        'total_points'
    ];

    protected $updatedField = 'updated_at';


    protected $validationRules = [
        // 'id'     => 'is_unique[products.id]',
        // 'name'        => 'required|is_unique[products.name]|min_length[3]',
        'author_id'        => 'required',
        'exhibition_id'        => 'required',
        'category_id'        => 'required',
    ];
    protected $validationMessages = [
        
    ];

    protected $beforeInsert   = ['generateId'];
    
    protected function generateId($data)
    {
        if (isset($data['data']['id'])) {
            return $data;
        }
        
        $data['data']['id'] = uniqid();
        return $data;
    }
  
 
    public function getProducts($id = false)
    {
        $result = $this->orderBy('id', 'DESC')->findAll();
        return $result;
    }


    public function addProduct($data)
    {
        try {
            return $this->insert($data, true);
        } catch(Exception $e) {
            throw $e;
        }
        
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


	public function getProductDetail($productId): object
	{
		$authorQuery = $this
			->select('products.id, usr.name as user_name, usr.bio, usr.image_url as avatar, usr.id as user_id, ctg.category_name as category_name, ctg.id category_id, usr.email, products.name as name, products.created_at, products.updated_at,
			products.description, products.total_points')
			->join('users usr', 'usr.id = products.author_id')
			->join('categories as ctg', 'ctg.id = products.category_id')
			->where('products.id', $productId)->first();

		return (object) $authorQuery;

    }


	function getBiggestPointOfProducts($categoryId) 
	{
		try {
			$query = $this->select('
				products.id, products.name, products.description,  products.total_points, products.created_at, products.updated_at, , 
				c.id category_id, c.category_name')
			->join('categories c', 'c.id = products.category_id')
			->where('category_id', $categoryId)->orderBy('total_points', 'desc')->limit(5);
			$result = $query->get()->getResult();
			return $result;
		} catch (Exception $e) {
			throw $e;
		}

	}

	/**
	 * @return mixed
	 */
	public function listProductsWithThumbs()
	{
		try {
            $q = $this->select()->join('product_thumbnails pt', 'pt.product_id = products.id')->groupBy('products.name');
            $r = $q->get()->getResult();
			return $r;
        } catch (Exception $e) {
			throw $e;
        }
	}



	public function getProductOfAuthor($authorId)
	{
		try {
			$q = $this->select('*')->where('author_id', $authorId);
			$productOfAuhtor = $q->get()->getResult();
			if (!$productOfAuhtor) 
			{
				throw new Exception('this user no have product', 404);
			}
			return $productOfAuhtor;
        } catch (Exception $e) {
			throw $e;
        }
	}

    
}
