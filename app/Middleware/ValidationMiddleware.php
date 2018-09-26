<?php

namespace App\Middleware ;

class ValidationMiddleware extends Middleware {

	public function __invoke($request, $response, $next) {
		// $this->container->view->getEnvironment()->addGlobal("errors",$_SESSION['errors']);
		// unset($_SESSION['errors']);
		if(isset($_SESSION['errors'])){
			$this->view()->getEnvironment()->addGlobal('errors',$_SESSION['errors']);
			unset($_SESSION['errors']);
		}
		$response = $next($request, $response);
		return $response;
	}

}