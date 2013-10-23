<?php

$routes = array(
	'apply' => 'templates/apply.php',
	'translate' => 'templates/translate.php',
	'credits' => 'templates/credits.php',
	'contact' => 'templates/contact.php',
	'privacy' => 'templates/privacy.php',
	//'review/bulkmail' => 'admin/bulk_mail.php',
	'review/country/grid' => 'review/country_grid.php',
	'review/country/edit' => 'review/edit_country.php',
	'review/country' => 'review/country_grid.php',
	'review/region' => 'review/region_grid.php',
	//'review/dump' => 'admin/dump.php',//?
	'review/edit' => 'review/edit.php',
	'review/export' => 'review/export.php',
	'review/grid' => 'review/grid.php',
	'review/grid/score' => 'review/grid_score.php',
	'review/phase1' => 'review/grid.php',
	'review/phase2' => 'review/grid_phase2.php',
	'review/search/results' => 'review/search_results.php',
	'review/search' => 'review/searchform.php',
	'review/view' => 'review/view.php',
	'review' => 'review/grid.php',
	'user/add' => 'admin/add_user.php',
	'user/list' => 'admin/user_grid.php',
	'user/login' => 'user/login.php',
	'user/logout' => 'user/logout.php',
	'user/password/reset' => 'user/user_pwreset.php',
	'user/password' => 'user/user_pwreset.php',
	'user/table' => 'admin/usertable.php',
	'user/view' => 'admin/view_user.php',
	'user' => 'user/login.php',
	'review/p1/successList' => 'review/phase1SuccessList.php',
	'review/p1/failList' => 'review/phase1FailList.php',
	'review/p2/list' => 'review/phase2List.php',
	'review/mail' => 'review/mail.php'
);

$defaultRoute = $routes['apply'];
