<?php

require_once '../src/init.php';

$router = new Router( $BASEURL, $routes, $defaultRoute );
$path = $router->route();
$basepath = array_search( $path, $routes );

include $path;
