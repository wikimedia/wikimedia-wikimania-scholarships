<?php

define( 'WMSCHOLS', true );

require_once( "includes/init.php" );

$path = $wgRouter->route();
$basepath = array_search($path, $routes);
$baselink = $BASEURL . $basepath;

include $path;
?>
