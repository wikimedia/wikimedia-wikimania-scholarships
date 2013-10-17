<?php
// save as config.php

$db_host = 'localhost';
$db_user = 'wikimania';
$db_pass = 'password';
$db_name = 'scholarships';
$db_driver = 'mysql';

$open_time = gmmktime(0, 0, 0, /*january*/ 1, /*1st*/ 1, 2012);
$close_time = gmmktime(0, 0, 0, /*February*/ 2, /*23rd*/ 23, 2013);

$email_from = 'wikimania-scholarships@wikimedia.org';

$chapters_application = false;

$TEMPLATEDIR = BASEDIR . '/templates/';
$TEMPLATEBASE = 'templates/';
$BASEURL = "/";
$TEMPLATEBASE_REVIEW = $BASEURL . 'templates/';

$mock = false;
