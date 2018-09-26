<?php

namespace App\Controllers ;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class Controller {

	protected $container;

	public function __construct($container) {
		$this->container = $container;
	}

	public function __get($attribute) {
		if($this->container->{$attribute}){
			return $this->container->{$attribute};
		}
	}
    
}