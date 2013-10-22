<?php

$routes = array(
	'apply' => $TEMPLATEDIR . 'apply.php',
	'translate' => $TEMPLATEDIR . 'translate.php',
	'credits' => $TEMPLATEDIR . 'credits.php',
	'contact' => $TEMPLATEDIR . 'contact.php',
	'privacy' => $TEMPLATEDIR . 'privacy.php',
	//'review/bulkmail' => BASEDIR . '/admin/bulk_mail.php',
	'review/country/grid' => BASEDIR . '/review/country_grid.php',
	'review/country/edit' => BASEDIR . '/review/edit_country.php',
	'review/country' => BASEDIR . '/review/country_grid.php',
	'review/region' => BASEDIR . '/review/region_grid.php',
        //'review/dump' => BASEDIR . '/admin/dump.php',//?
	'review/edit' => BASEDIR . '/review/edit.php',
	'review/export' => BASEDIR . '/review/export.php',
	'review/grid' => BASEDIR . '/review/grid.php',
	'review/grid/score' => BASEDIR . '/review/grid_score.php',
	'review/phase1' => BASEDIR . '/review/grid.php',
	'review/phase2' => BASEDIR . '/review/grid_phase2.php',
        'review/search/results' => BASEDIR . '/review/search_results.php',
	'review/search' => BASEDIR . '/review/searchform.php',
	'review/view' => BASEDIR . '/review/view.php',
	'review' => BASEDIR . '/review/grid.php',
        'user/add' => BASEDIR . '/admin/add_user.php',
	'user/list' => BASEDIR . '/admin/user_grid.php',
	'user/login' => BASEDIR . '/user/login.php',
	'user/logout' => BASEDIR . '/user/logout.php',
	'user/password/reset' => BASEDIR . '/user/user_pwreset.php',
	'user/password' => BASEDIR . '/user/user_pwreset.php',
	'user/table' => BASEDIR . '/admin/usertable.php',
	'user/view' => BASEDIR . '/admin/view_user.php',
	'user' => BASEDIR . '/user/login.php',
	'review/p1/successList' => BASEDIR . '/review/phase1SuccessList.php',
	'review/p1/failList' => BASEDIR . '/review/phase1FailList.php',
	'review/p2/list' => BASEDIR . '/review/phase2List.php',
	'review/mail' => BASEDIR . '/review/mail.php'
);

$defaultRoute = $routes['apply'];
