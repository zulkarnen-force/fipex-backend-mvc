<?php

namespace App\Api\Domains\GuestBook\Repository;

use App\Api\Domains\Common\Repository\SqlCommonRepository;

class SqlGuestBookRepository extends SqlCommonRepository implements IGuestBookRepository
{
	public $model; 
	public function __construct()
    {
		parent::__construct('\App\Api\Domains\GuestBook\Model\GuestBook');
		$this->model = model('\App\Api\Domains\GuestBook\Model\GuestBook');
	}
	
	public function list($fields = ['*'])
	{
		$q = $this->model->orderBy('created_at', 'DESC');
		$r = $q->get()->getResult();
		return $r;
	}
}