<?php

class Router {

	public $routes;

	public $class;

	public $method;

	protected $request;

	protected $baseUrl;

	protected $defaultRoute;

	public function __construct( $baseUrl, $routes, $defaultRoute ) {
		$this->baseUrl = $baseUrl;
		$this->request = Request::newFromGlobals();
		$this->routes = $routes;
		$this->defaultRoute = $defaultRoute;
	}

	public function isValid($page) {
		if ( array_key_exists( $page, $this->routes ) ) {
			return true;
		}
		return false;
	}

	public function route() {
		$server = $this->request->getServer();

		// separate query string, get base request
		$parts = explode( '?', $server['REQUEST_URI'] );
		$basereq = explode( $this->baseUrl, $parts[0] );

		$reqjoin = join( $basereq, '/' );
		$req = explode( '/', $reqjoin );

		while ( isset( $req[0] ) && ( $req[0] == "index.php" ||
			( strlen( $req[0] ) < 1 ) && count( $req ) > 0 )
		) {
			array_shift( $req );
		}

		$path = $this->getPath( $req );

		if ( $this->isValid( $path ) ) {
			return $this->routes[$path];
		} elseif ( $this->class && ( in_array( $this->class, array( 'review', 'user' ) ) ) ) {
			return $this->routes['user/login'];
		} else {
			return $this->defaultRoute;
		}
	}

	protected function getPath( $req ) {
		$methods = array();
		$path = '';

		$this->class = isset( $req[0] ) ? $req[0] : null;

		if ( strlen( $this->class ) > 0 ) {
			$path = $path . $this->class;
		}

		if ( isset( $req[1] ) ) {
			$methods[] = $req[1];

			if ( isset( $req[2] ) ) {
				$methods[] = $req[2];
			}
		}

		foreach( $methods as $method ) {
			$path .= '/' . $method;
		}

		return $path;
	}

}
