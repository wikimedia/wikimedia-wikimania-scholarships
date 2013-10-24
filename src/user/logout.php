<?php
session_start();
$_SESSION['user_id'] = false;
session_unset();
session_destroy();
header('location: ' . $BASEURL);
exit();
