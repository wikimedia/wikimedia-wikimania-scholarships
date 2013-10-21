<?php

class Router {

	public $qs;

	public $config;

	public $routes;

	public $class;

	public $method;

	public $directory;

	public $default_controller;

	public function __construct() {	
		global $routes;
		$this->routes = $routes;
	}

	public function isValid($page) {
		if ( array_key_exists( $page, $this->routes ) ) {
			return true;
		} 
		return false;
	}

	public function route() {
		global $defaultRoute, $BASEURL;

		# separate query string, get base request
		$parts = explode('?', $_SERVER['REQUEST_URI']);
                $basereq = explode($BASEURL, $parts[0]);
	
		$reqjoin = join($basereq, '/');
		$req = explode('/', $reqjoin);

		while ( ( ( $req[0] == "index.php" ) || ( strlen($req[0]) < 1 ) ) && ( count($req) > 0 ) ) {
			array_shift($req);
		}

		$this->class = isset($req[0]) ? $req[0] : null;
		$this->method = isset($req[1]) ? $req[1] : null;
		$this->method2 = isset($req[2]) ? $req[2] : null;

		$path = '';		
		if ( strlen( $this->class ) > 0 ) {
			$path = $path . $this->class;
		}

		if ( strlen( $this->method ) > 0 ) {
			$path = $path . '/' . $this->method;
		}

		if ( strlen( $this->method2 ) > 0 ) {
			$path = $path . '/' . $this->method2;
		}

		if ( $this->isValid( $path ) ) {
			return $this->routes[$path];
		} elseif ( ( $this->class == 'review' ) || ( $this->class == 'user' ) ) {
			return $this->routes['user/login'];
		} else {
			return $defaultRoute;
		}
	}	
}	
