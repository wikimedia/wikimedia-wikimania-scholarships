<?php

define( 'WMSCHOLS', true );

require_once( "includes/init.php" );

$router = new Router( BASEDIR, $routes, $defaultRoute );
$path = $router->route();
$basepath = array_search( $path, $routes );
$baselink = $BASEURL . $basepath;

include $path;
