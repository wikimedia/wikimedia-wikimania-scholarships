<?php

# set paths
$system_path = 'system';
$include_path = 'includes';
$template_path = 'templates';
$application_path = 'application';

#define('BASEDIR', dirname(__FILE__) );
#define('BASEDIR', '/var/www/dev/schols');
#define('BASEDIR', '/mnt/ms/home/web/u00035/html/scholstest');
define('BASEDIR', dirname(dirname(__FILE__) ));

define('SYSPATH', BASEDIR . '/' . $system_path . '/');
define('INCLUDEPATH', BASEDIR . '/' . $include_path . '/');
define('TEMPLATEPATH', BASEDIR . '/' . $template_path . '/');
define('APPPATH', BASEDIR . '/' . $application_path . '/');

require_once( SYSPATH . "core/Common.php" );

require_once( INCLUDEPATH . "config.php" );
require_once( INCLUDEPATH . "helper.php" );
require_once( INCLUDEPATH . "db.php" );
require_once( INCLUDEPATH . "variables.php" );
require_once( INCLUDEPATH . "schema.php" );
require_once( INCLUDEPATH . "pagination.php" );
require_once( INCLUDEPATH . "Lang.php" );
require_once( INCLUDEPATH . "Application.php" );
require_once( SYSPATH . "core/Router.php" );
require_once( INCLUDEPATH . "routes.php" );

// PEAR DB
require_once( 'DB.php' );

$wgLang = new Lang();
$wgRouter = new Router();
