<?php
// save as config.php

$CONFIG = array(
	'db' => array(
		'dsn' => 'mysql:host=localhost;dbname=REPLACE_DB',
		'user' => '',
		'password' => '',
	),
);

$open_time = gmmktime( 0, 0, 0, /*january*/ 1, /*1st*/ 1, 2011 );
$close_time = gmmktime( 0, 0, 0, /*february*/ 2, /*1st*/ 15, 2012 );

$email_from = 'wikimania-scholarships@wikimedia.org';

$chapters_application = FALSE;

$TEMPLATEDIR = BASEDIR . '/templates/';
$TEMPLATEBASE = '/templates/';
$BASEURL = "/";
$TEMPLATEBASE_REVIEW = $BASEURL . 'templates/';

$mock = true;
