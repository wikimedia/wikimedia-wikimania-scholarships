<?php
// save as config.php

$db_host = 'localhost';
$db_user = 'uwikimania';
$db_pass = '09ft9kkj';
$db_name = 'dbwikimania';
$db_driver = 'mysql';

$open_time = gmmktime(0, 0, 0, /*january*/ 1, /*1st*/ 1, 2012);
$close_time = gmmktime(0, 0, 0, /*February*/ 2, /*23rd*/ 23, 2013);

$email_from = 'wikimania-scholarships@wikimedia.org';

$chapters_application = FALSE;


$TEMPLATEDIR = BASEDIR . '/templates/';
$TEMPLATEBASE = 'templates/';
$BASEURL = "/";
$TEMPLATEBASE_REVIEW = $BASEURL . 'templates/';

$mock = FALSE;
?>
