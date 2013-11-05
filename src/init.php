<?php
// FIXME: should be done in apache config
date_default_timezone_set( 'Europe/London' );

// FIXME: Set include_path externally?
//set_include_path( dirname( __FILE__ ) . PATH_SEPARATOR . get_include_path() );

// load our configuration
require_once dirname( __FILE__ ) . '/config.php';

// FIXME: autoloader!
//require_once 'routes.php';

