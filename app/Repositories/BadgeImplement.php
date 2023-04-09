<?php
namespace App\Repositories;

class BadgeImplement implements BadgeInterface 
{
    
	/**
	 * @return mixed
	 */
	public function send()
    {
        echo 'sending badge';
	}
}