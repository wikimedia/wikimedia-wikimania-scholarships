<?php
// save as config.php

$db_host = 'localhost';
$db_user = '';
$db_pass = '';
$db_name = '';
$db_driver = 'mysql';

$open_time = gmmktime(0, 0, 0, /*january*/ 1, /*1st*/ 1, 2011);
$close_time = gmmktime(0, 0, 0, /*february*/ 2, /*1st*/ 15, 2012);

$email_from = 'wikimania-scholarships@wikimedia.org';

$chapters_application = FALSE;

$TEMPLATEDIR = BASEDIR . '/templates/';
$TEMPLATEBASE = '/templates/';
$BASEURL = "/";
$TEMPLATEBASE_REVIEW = $BASEURL . 'templates/';

$mock = true;
?>
