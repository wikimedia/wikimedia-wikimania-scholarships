<?php
// FIXME: should be done in apache config
date_default_timezone_set( 'Europe/London' );

// FIXME: Set include_path externally?
set_include_path( dirname( __FILE__ ) . PATH_SEPARATOR . get_include_path() );

// load our configuration
require_once 'config.php';

// FIXME: autoloader!
require_once 'helper.php';
require_once 'routes.php';
require_once 'variables.php';

require_once 'AbstractDao.php';
require_once 'Common.php';
require_once 'Dao.php';
require_once 'Pagination.php';
require_once 'Lang.php';
require_once 'Application.php';
require_once 'Request.php';
require_once 'Router.php';
require_once 'TemplateHelper.php';
require_once 'Password.php';
require_once 'User.php';

