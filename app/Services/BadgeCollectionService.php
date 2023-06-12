<?php

namespace App\Services;

use App\Models\BadgeCollection;
use App\Models\BadgeInventory;
use App\Models\Product;
use App\Models\ProductMember;
use App\Models\User;
use App\Exceptions\ValidationException;
use App\Utils\Response;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;
use Throwable;

class BadgeCollectionService
{
    public $model;
    function __construct(BadgeCollection $model)
    {
        $this->model = $model;
    }


    public function find($id)
    {
        try {
            $badges = $this->model->findById($id);
            return new Response(200, null, true, $badges);
        } catch (Exception $th) {
            return new Response(404, $th->getMessage(), false);
        }
    }


    public function list()
    {
        try {
            $badgesCollections = $this->model->list();
            return new Response(200, 'data of all badges collections', true, $badgesCollections);
        } catch (Throwable $th) {
            return new Response(200, $th->getMessage(), false, null);
        }
    }


    public function create($data)
    {
        try {
            $insertedData = $this->model->store($data);
            if ($insertedData === false) {
                return new Response(400, 'error insert data', false, null, null);
            }
            return new Response(201, 'badge collection created successfully successfully', true, null, null);
        } catch (ValidationException $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, $e->getErrors());
        } catch (Exception $e){
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        }

    }


    public function update(string $id, $data = [])
    {
        try {
            $this->model->findById($id);
            $userUpdated = $this->model->updateById($id, $data);
            if ($userUpdated === false) {
                throw new Exception('error on update data');
            }
            return new Response(201, 'badge collection updated successfully', true, $userUpdated);
        } catch (Exception $th) {
            return new Response(400, $th->getMessage(), false, null, null);
        }
    }



    public function delete($id)
    {
        try {
            $response = $this->model->findById($id);
            $deleted = $this->model->deleteById($id);
            return new Response(200, 'exhibition deleted', true, $response, null);
        } catch (DatabaseException $th) {
            return new Response($th->getCode(), $th->getMessage(), false, null);
        } catch (Exception $th) {
            return new Response(404, $th->getMessage(), false, null);
        }
    }


    public function getById($id)
    {
        try {
            $result = $this->model->findById($id);
            return new Response(200, 'product data', true, $result);
        } catch (Exception $e) {
            return new Response(400, $e->getMessage(), false);
        }
    }


    public function getProductByAuthorId(string $userId, array $fields = ["*"])
    {
        $result = $this->model->getByQuery(['author_id' => $userId], $fields);
        if ($result === false) {
            return new Response(404, 'product not found', false, null, null);
        };
        return new Response(200, 'product of user', true, $result, null);
    }

    public function getBadgeByProductId($productId)
    {
        $result = $this->model->getBadges($productId);

       // TODO: please refactor my bad if else 
        $result = array_map(function ($badge) {
            $badge->badges_total = (int) $badge->badges_total;
           if ($badge->type === 'platinum') {
                $badge->points_total = $badge->badges_total * 100;
            } else if  ($badge->type === 'gold') {
                $badge->points_total = $badge->badges_total * 50;
            } else if ($badge->type === 'silver'){
                $badge->points_total = $badge->badges_total * 20;
            }
            return $badge;
        }, $result);

        return $result;
    }


    public function getCommentsOfProduct($productId)
    {
        $badgeCollection = $this->model->getComments($productId);

        $commentsOfProduct = array_map(function ($badge)  {
            $user = ['id' => $badge->user_id, 'name' => $badge->name, 'avatar' => $badge->avatar, 'bio' => $badge->bio];
            $timestamps = ['created_at' => $badge->created_at, 'updated_at' => $badge->updated_at];
            $badge->created_by = $user;
            $badge->timestamps = $timestamps;
            unset($badge->name, $badge->created_at, $badge->updated_at, $badge->avatar,  $badge->bio, $badge->user_id);
            
            return $badge;
            
        }, $badgeCollection);

        return $commentsOfProduct;
    }



    public function decrementBadgeUser($userId)
    {
        try {
            $result = $this->model->decrementBadgeUser($userId);
            if ($result) {
                return $result;
            }
            return $result;
        } catch(Exception $e) {
            throw $e;
        }
    }


    private function isOwnedProductOfUser($userId, $productId)
    {
        $u = new User();
        $p = new Product();
        $pMember = new ProductMember();
        $r = $u->find($userId);
        if (!$r) {
            throw new Exception('user not found');
        }

        if ($r['is_author']) 
        {
            $qP = $p->where('author_id', $userId)->where('id', $productId);
            if ($qP->get()->getResult()) {
                return true;
            }
            return false;
        }
        $qPM = $pMember->where('user_id', $userId)->where('product_id', $productId);
        $productOwnedMember = $qPM->get()->getResult();
        if ($productOwnedMember) {
            return true;
        }
        return false;

    }

    public function sendBadgeToProduct($request)
    {
        try {
            $userId = $request['user_id'];
            $productId = $request['product_id'];
            $bInventory = new BadgeInventory();
            $badgeInventoryUser = $bInventory->where(['user_id' => $userId])->get()->getResult();
            $request['badge_type'] = $badgeInventoryUser[0]->badge_type;
            $request['exhibition_id'] = $badgeInventoryUser[0]->exhibition_id;
            $isHasGivenBadge = $this->model->isUserHasGivenBadge($userId, $productId);
            $productOwned = $this->isOwnedProductOfUser($userId, $productId);
            if ($productOwned) {
                return new Response(401, 'users can only send badges to other products, but this request sends to the property of the user', false, null);
            }
            if ($isHasGivenBadge) {
                return new Response(400, 'your has been given badge to this product 🖐', false, null);
            }
        
            $this->model->store($request);
            $this->decrementBadgeUser($userId);

           $productData = $this->getBadgesWithTotalPoints($productId);
           $data = $productData->getData();
           $total_points = $data->total_points;
            
            $productModel = new Product();
            $productModel->update($productId, ['total_points' => $total_points]);

            return new Response(200, 'badge derived successfully',  true, null);
            
        } catch (ValidationException $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, $e->getErrors());
        } catch (Exception $e){
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        }
    }

    public function cancleBadgeOfUser($userId, $productId)
    {
        try {
           $result = $this->model->backBadgeFromProductToUser($userId, $productId);
           $productData = $this->getBadgesWithTotalPoints($productId);
           $data = $productData->getData();
           $total_points = $data->total_points;
           $productModel = new Product();
           $productModel->update($productId, ['total_points' => $total_points]);
            return new Response(200, 'badge cancle and back to user successfully 🏍',  true, null);
        } catch (ValidationException $e) {
            return new Response($e->getCode(), $e->getMessage(), false, null, $e->getErrors());
        } catch (Exception $e){
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        } catch (Throwable $e){
            return new Response($e->getCode(), $e->getMessage(), false, null, null);
        } 
    }


    function getBadgesWithTotalPoints($productId)
    {
        try {
            $badges = $this->model->getBadgesWithPoints($productId);
            $result = array_map(function ($d) {
                $d->gold = ['badge' => (int) $d->gold, 'points' => (int) $d->gold_points];
                $d->silver = ['badge' => (int) $d->silver, 'points' => (int) $d->silver_points];
                $d->platinum = ['badge' => (int) $d->platinum, 'points' => (int) $d->platinum_points];
                $d->total_points = $d->silver_points+$d->gold_points+$d->platinum_points;
                unset($d->gold_points, $d->silver_points, $d->platinum_points);
                return $d;
            }, $badges);
            return new Response(200, 'badges of product', true, $result[0], null);
        } catch (Throwable $th) {
            return new Response($th->getCode(), $th->getMessage(), false, null, null);
        }
    }


}

?>