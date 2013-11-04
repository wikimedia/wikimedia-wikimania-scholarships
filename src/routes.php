<?php

/**
 * URI to activity mappings.
 * @var array $routes
 */
$routes = array(
	'review/bulkmail'       => 'admin/bulk_mail.php',
	'review/country'        => 'review/country_grid.php',
	'review/country/edit'   => 'review/edit_country.php',
	'review/country/grid'   => 'review/country_grid.php',
	'review/dump'           => 'admin/dump.php',           // usecase unknown
	'review/export'         => 'review/export.php',
	'review/grid/score'     => 'review/grid_score.php',
	'review/mail'           => 'review/mail.php',
	'review/p1/failList'    => 'review/phase1FailList.php',
	'review/p1/successList' => 'review/phase1SuccessList.php',
	'review/p2/list'        => 'review/phase2List.php',
	'review/region'         => 'review/region_grid.php',
	'review/search'         => 'review/searchform.php',
	'review/search/results' => 'review/search_results.php',
	'review/view'           => 'review/view.php',
	'user/add'              => 'admin/add_user.php',
	'user/list'             => 'admin/user_grid.php',
	'user/table'            => 'admin/usertable.php',
	'user/view'             => 'admin/view_user.php',
);

//$defaultRoute = $routes['apply'];
