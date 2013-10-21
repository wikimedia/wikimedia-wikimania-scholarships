<?php

class Router {

	public function isValid($page) {
		global $routes;
		if ( array_key_exists( $page, $routes ) ) {
			return true;
		} 
		return false;
	}

	public function route() {
		global $defaultRoute, $routes, $BASEURL;
		$parts = explode('?', $_SERVER['REQUEST_URI']);
                $mainreq = explode($BASEURL, $parts[0]);
	
		$reqjoin = join($mainreq, '/');
		$req = explode('/', $reqjoin);

		if ( ( $req[0] == "index.php" ) || ( strlen($req[0]) < 1 ) ) {
			array_shift($req);
		}

		$page = isset($req[0]) ? $req[0] : null;
		$action = isset($req[1]) ? $req[1] : null;
		$action2 = isset($req[2]) ? $req[2] : null;

		$path = '';		
		if ( strlen( $page ) > 0 ) {
			$path = $path . $page;
		}

		if ( strlen( $action ) > 0 ) {
			$path = $path . '/' . $action;
		}

		if ( strlen( $action2 ) > 0 ) {
			$path = $path . '/' . $action2;
		}

		if ( $this->isValid( $path ) ) {
			return $routes[$path];
		} elseif ( ( $page == 'review' ) || ( $page == 'user' ) ) {
			return $routes['user/login'];
		} else {
			return $defaultRoute;
		}
	}	
}	
