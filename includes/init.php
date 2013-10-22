<?php

// set paths
$system_path = 'system';
$include_path = 'includes';
$template_path = 'templates';
$application_path = 'application';

date_default_timezone_set( 'Europe/London' );

define( 'BASEDIR', dirname( dirname( __FILE__ ) ) );

define( 'SYSPATH', BASEDIR . '/' . $system_path . '/' );
define( 'INCLUDEPATH', BASEDIR . '/' . $include_path . '/' );
define( 'TEMPLATEPATH', BASEDIR . '/' . $template_path . '/' );
define( 'APPPATH', BASEDIR . '/' . $application_path . '/' );

require_once( __DIR__ . "/config.php" );
require_once( __DIR__ . "/helper.php" );
require_once( __DIR__ . "/routes.php" );
require_once( __DIR__ . "/db.php" );
require_once( __DIR__ . "/variables.php" );
require_once( __DIR__ . "/schema.php" );

require_once( __DIR__ . "/Common.php" );
require_once( __DIR__ . "/helper.php" );
require_once( __DIR__ . "/pagination.php" );
require_once( __DIR__ . "/Lang.php" );
require_once( __DIR__ . "/Application.php" );
require_once( __DIR__ . "/Request.php" );
require_once( __DIR__ . "/Router.php" );
require_once( __DIR__ . '/TemplateHelper.php' );

$wgLang = new Lang();
