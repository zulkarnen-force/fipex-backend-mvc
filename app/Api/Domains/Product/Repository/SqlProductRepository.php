<?php

namespace App\Api\Domains\Product\Repository;

use App\Api\Domains\Common\Repository\SqlCommonRepository;
use App\Models\ProductThumbnail;
use Exception;

class SqlProductRepository extends SqlCommonRepository implements IProductRepository
{
	public $model; 
	public function __construct()
    {
		parent::__construct('App\Api\Domains\Product\Model\Product');
		$this->model = model('App\Api\Domains\Product\Model\Product');
	}

	public function getProductDetail($productId): object
	{
		$authorQuery = $this->model
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
			$query = $this->model->select('
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
            $q = $this->model->select()->join('product_thumbnails pt', 'pt.product_id = products.id')->groupBy('products.name');
            $r = $q->get()->getResult();
			return $r;
        } catch (Exception $e) {
			throw $e;
        }
	}



	public function getProductOfAuthor($authorId)
	{
		try {
			$q = $this->model->select('*')->where('author_id', $authorId);
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